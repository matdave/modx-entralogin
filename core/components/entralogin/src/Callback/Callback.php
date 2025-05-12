<?php

namespace MODX\EntraLogin\Callback;

use Exception;
use MODX\EntraLogin\Service;
use MODX\EntraLogin\Services\Entra;
use MODX\Revolution\Mail\modMail;
use MODX\Revolution\Mail\modPHPMailer;
use MODX\Revolution\modUser;
use MODX\Revolution\modUserGroup;
use MODX\Revolution\modUserGroupRole;
use MODX\Revolution\modUserProfile;
use MODX\Revolution\modUserSetting;
use MODX\Revolution\modX;
use xPDO\xPDO;

class Callback
{
    protected Service $service;

    /** @var modX */
    public $modx = null;

    public Entra $client;

    public function __construct($service)
    {
        $this->service = $service;
        $this->modx = $service->modx;
        $this->service->loadClient();
        $this->client = $this->service->client;
    }

    /**
     * @throws Exception
     */
    public function handleCallback(): void
    {
        if (empty($this->client)) {
            throw new Exception($this->modx->lexicon('entralogin.error.client'));
        }

        if (isset($_POST['code']) && isset($_POST['state'])) {
            $tag = $this->client->getTag($_POST['state']);
            if (empty($tag)) {
                throw new Exception($this->modx->lexicon('entralogin.error.tag'));
            }
            $codeVerifier = $tag['codeVerifier'];
            $req = $this->client->getAccessToken($_POST['code'], $codeVerifier);
            $resp = json_decode($req, true);
            if (empty($resp) || !isset($resp['access_token'])) {
                throw new Exception($this->modx->lexicon('entralogin.error.access_token', ['error' => $req]));
            }
            $this->client->setAccessToken([
                'token' => $resp['access_token'],
                'created' => strtotime('now'),
                'expires_in' => (int) $resp['expires_in'] ?? 0,
                'refresh_token' => $resp['refresh_token'] ?? '',
            ]);
            $this->loginUser();
        }
        if (isset($_POST['id_token'])) {
            // validate the jwt
            if ($this->client->isIdTokenValid($_POST['id_token'])) {
                $this->client->setAccessToken([
                    'token' => $_POST['access_token'],
                    'created' => strtotime('now'),
                    'expires_in' => (int) $_POST['expires_in'] ?? 0,
                    'refresh_token' => '',
                ]);
                $this->loginUser();
            }
        }
        if (!empty($_SESSION['elog_access_token'])) {
            $this->client->setAccessToken($_SESSION['elog_access_token']);
            if ($this->client->isTokenExpired()) {
                $req = $this->client->getAccessTokenRefresh($_SESSION['elog_access_token']['refresh_token']);
                $resp = json_decode($req, true);
                if (empty($resp) || !isset($resp['access_token'])) {
                    throw new Exception($this->modx->lexicon('entralogin.error.refresh_token', ['error' => $req]));
                }
                $this->client->setAccessToken([
                    'token' => $resp['access_token'],
                    'created' => strtotime('now'),
                    'expires_in' => (int) $resp['expires_in'] ?? 0,
                    'refresh_token' => $resp['refresh_token'] ?? '',
                ]);
            }
            $this->loginUser();
        }
        $this->sendManager();
    }

    private function sendManager($success = false, $params = []): void
    {
        $extParams = '';
        foreach ($params as $key => $value) {
            $extParams .= "&$key=$value";
        }
        $this->modx->sendRedirect($this->modx->getOption('manager_url'). '?entralogin=' . ($success ? 'success' : 'fail') . $extParams);
    }

    /**
     * @throws Exception
     */
    private function loginUser(): void
    {
        $me = $this->client->me('/me');
        $me = json_decode($me, true);
        // search for user setting
        $userSetting = $this->modx->getObject(modUserSetting::class,
            ['key' => 'entralog_id', 'value' => $me['id']]
        );
        if (!empty($userSetting)) {
            $this->syncUserDetails($userSetting->get('user'), $me);
            $this->loginUserWithID($userSetting->get('user'));
            return;
        } elseif ($this->modx->user->isAuthenticated('mgr')) {
            $this->addUserSetting($this->modx->user->get('id'), $me['id']);
            $this->sendManager(true, ['a' => 'security/profile']);
            return;
        }
        if ($this->modx->getOption('entralogin.allow_match_by_email', [], false)) {
            $userByEmail = $this->modx->getObject(modUserProfile::class, ['email' => $me['mail']]);
            if (!empty($userByEmail)) {
                $this->addUserSetting($userByEmail->get('internalKey'), $me['id']);
                $this->syncUserDetails($userByEmail->get('internalKey'), $me);
                $this->loginUserWithID($userByEmail->get('internalKey'));
                return;
            }
        }
        if ($this->modx->getOption('entralogin.allow_signup', [], false)) {
            $this->signupUser($me);
        }
        $this->sendManager();
    }

    private function loginUserWithID(int $id): void
    {
        $user = $this->modx->getObject(modUser::class, $id);
        if (!empty($user)) {
            $this->loadUser($user);
        }
    }

    private function loadUser(modUser $user): void
    {
        $targets = explode(',', $this->modx->getOption('principal_targets', null,
            'MODX\\Revolution\\modAccessContext,MODX\\Revolution\\modAccessResourceGroup,MODX\\Revolution\\modAccessCategory,MODX\\Revolution\\Sources\\modAccessMediaSource,MODX\\Revolution\\modAccessNamespace'));
        array_walk($targets, 'trim');
        if ($user->get('active') === 0 || $user->get('blocked') === 1) {
            $this->sendManager();
            return;
        }
        $this->modx->user = $user;
        $this->modx->user->addSessionContext('mgr');
        $this->modx->user->loadAttributes($targets, 'mgr', true);
        $this->sendManager(true);
    }

    private function addUserSetting(int $modxId, $value, $key = 'entralog_id'): void
    {
        $setting = $this->modx->newObject(modUserSetting::class);
        $setting->set('user', $modxId);
        $setting->set('key', $key);
        $setting->set('value', $value);
        $setting->save();
    }

    private function signupUser(array $user): void
    {
        $domains = $this->modx->getOption('entralogin.allow_signup_domains', null, '');
        $domains = explode(',', $domains);
        array_walk($domains, 'trim');
        $userDomain = explode('@', $user['mail']);
        if (!empty($domains) && !in_array($userDomain[1], $domains)) {
            $this->sendManager();
            return;
        }
        $active = (int) $this->modx->getOption('entralogin.allow_signup_active', null, 0);
        $defaultGroup = $this->modx->getOption('entralogin.default_group', null, null);
        $defaultRole = $this->modx->getOption('entralogin.default_role', null, 'Member');
        $groupID = 0;
        $roleID = 0;
        if (!empty($defaultGroup)) {
            $group = $this->modx->getObject(modUserGroup::class, ['name' => $defaultGroup]);
            if (!empty($group)) {
                $groupID = $group->get('id');
            }
        }
        if (!empty($groupID)) {
            $role = $this->modx->getObject(modUserGroupRole::class, ['name' => $defaultRole]);
            if (!empty($role)) {
                $roleID = $role->get('id');
            }
        }
        $newUser = $this->modx->newObject(modUser::class);
        $newUser->fromArray([
            'username' => $user['userPrincipalName'],
            'active' => $active,
            'blocked' => 0,
            'remote_key' => $user['id'],
            'primary_group' => $groupID,
        ]);
        $newUser->save();
        if ($groupID && $roleID) {
            $newUser->joinGroup($groupID, $roleID);
        }
        $this->addUserSetting($newUser->get('id'), $user['id']);
        $language = explode('-', $user['preferredLanguage']);
        $this->addUserSetting($newUser->get('id'), $language[0], 'manager_language');
        $this->modx->newObject(modUserProfile::class, [
            'internalKey' => $newUser->get('id'),
            'fullname' => $user['displayName'],
            'email' => $user['mail'],
            'mobilephone' => $user['mobilePhone'],
        ])->save();
        $notify = $this->modx->getOption('entralogin.allow_signup_notify', null, '');
        $notify = explode(',', $notify);
        array_walk($notify, 'trim');
        if (!empty($notify)) {
            $body = $this->modx->lexicon('entralogin.email.body', [
                'site_name' => $this->modx->getOption('site_name'),
                'email' => $user['mail'],
            ]);
            $subject = $this->modx->lexicon('entralogin.email.subject');
            $mail = new modPHPMailer($this->modx);
            $mail->set(modMail::MAIL_BODY, $body);
            $mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
            $mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
            $mail->set(modMail::MAIL_SUBJECT, $subject);
            foreach ($notify as $email) {
                $mail->address('to', $email);
            }
            $mail->address('reply-to', $this->modx->getOption('emailsender'));
            $mail->setHTML(true);
            if (!$mail->send()) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, $this->modx->lexicon('entralogin.error.email', ['error' =>print_r($mail->mailer->ErrorInfo, true)]));
            }
            $mail->reset();
        }
        if ($active) {
            $this->loadUser($newUser);
        } else {
            $this->sendManager(true, ['signup' => '1']);
        }
    }

    private function syncUserDetails(int $id, array $user): void
    {
        $modUser = $this->modx->getObject(modUser::class, $id);
        if (!empty($modUser)) {
            $profile = $modUser->getOne('Profile');
            if (!empty($profile)) {
                $profile->set('fullname', $user['displayName']);
                $profile->set('email', $user['mail']);
                $profile->set('mobilephone', $user['mobilePhone']);
                $profile->save();
            }
        }
    }

}