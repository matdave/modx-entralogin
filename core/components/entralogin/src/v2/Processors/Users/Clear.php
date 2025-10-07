<?php

namespace MODX\EntraLogin\v2\Processors\Users;

class Clear extends \modProcessor
{
    public $languageTopics = array('entralogin:default');
    public $objectType = 'user_setting';

    public function process()
    {
        $id = $this->getProperty('user');

        if (empty($id)) {
            return $this->failure($this->modx->lexicon('entralogin.error.user_not_found'));
        }
        if (!is_array($id)) {
            $id = explode(',', $id);
        }

        $this->modx->removeCollection('modUserSetting', ['user:IN' => $id, 'key' => 'entralog_id']);

        return $this->success();
    }
}