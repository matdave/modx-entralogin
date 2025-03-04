<?php

$_lang['entralogin'] = 'Entra Login';
$_lang['entralogin.desc'] = 'Entra Login for MODX Revolution';

$_lang['entralogin.glog_success'] = '<p class="is-success">Successfully logged in with Entra</p>';
$_lang['entralogin.glog_fail'] = '<p class="is-error">Error logging in with Entra</p>';
$_lang['entralogin.glog_signup'] = '<p class="is-success">You have signed up and a manager will review your access.</p>';
$_lang['entralogin.login_with_entra'] = 'Login with Entra';
$_lang['entralogin.connect_entra'] = 'Connect Entra Account';
$_lang['entralogin.disconnect_entra'] = 'Disconnect Entra Account';
$_lang['entralogin.disable_regular_login'] = 'Regular login is disabled';
$_lang['entralogin.disable_regular_login_warning'] = '<h2 class="warning">Warning</h2><p>Regular login is disabled. <a href="[[+link]]">Please connect your account on your profile page.</a></p>';

$_lang['entralogin.email.subject'] = 'New manager signed up with Entra';
$_lang['entralogin.email.body'] = 'New manager signed up on [[+site_name]] with Entra: [[+email]]';

$_lang['entralogin.error.email'] = 'An error occurred while trying to send the email: [[+error]]';
$_lang['entralogin.error.access_token'] = 'Error getting access token: [[+error]]';
$_lang['entralogin.error.refresh_token'] = 'Error getting refresh token: [[+error]]';
$_lang['entralogin.error.tag'] = 'Tag not set, make sure your session_cookie_samesite is set to Lax or empty in the system settings';
$_lang['entralogin.error.client'] = 'Client not set, please check your system settings.';

$_lang['setting_entralogin.client_id'] = 'Client ID';
$_lang['setting_entralogin.client_id_desc'] = 'Client ID for Oauth2 from your Entra Developer Console';
$_lang['setting_entralogin.client_secret'] = 'Client Secret';
$_lang['setting_entralogin.client_secret_desc'] = 'Client Secret for Oauth2 from your Entra Developer Console';
$_lang['setting_entralogin.allow_signup'] = 'Allow new signups';
$_lang['setting_entralogin.allow_signup_desc'] = 'Allow new managers to signup with Entra. (Warning: this will allow anyone with a Entra account to sign up. Use with caution.)';
$_lang['setting_entralogin.allow_signup_domains'] = 'Domain restrictions';
$_lang['setting_entralogin.allow_signup_domains_desc'] = 'Domain restrictions for new signups. Comma separated list of domains.';
$_lang['setting_entralogin.allow_signup_active'] = 'Default active';
$_lang['setting_entralogin.allow_signup_active_desc'] = 'Default active state for new signups.';
$_lang['setting_entralogin.allow_signup_notify'] = 'Notify email(s)';
$_lang['setting_entralogin.allow_signup_notify_desc'] = 'Emails to notify of new signups. Comma separated list of emails.';
$_lang['setting_entralogin.allow_match_by_email'] = 'Email matching';
$_lang['setting_entralogin.allow_match_by_email_desc'] = 'Allow matching existing users by email address.';
$_lang['setting_entralogin.default_group'] = 'Default group';
$_lang['setting_entralogin.default_group_desc'] = 'Default group for new signups.';
$_lang['setting_entralogin.default_role'] = 'Default Role';
$_lang['setting_entralogin.default_role_desc'] = 'Default Role for new signups.';
$_lang['setting_entralogin.disable_regular_login'] = 'Disable regular login';
$_lang['setting_entralogin.disable_regular_login_desc'] = 'Disable the regular login form options. (Warning: this will disable the regular login form for all users.)';
