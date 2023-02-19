<?php

namespace aigletter\logging\models;

use yii\db\ActiveRecord;

/**
 * @property $remoteAddr;
 * @property $remoteUser;
 * @property $timeLocal;
 * @property $request;
 * @property $status;
 * @property $bodyBytesSent;
 * @property $httpReferer;
 * @property $httpUserAgent;
 */
class Log extends ActiveRecord
{
    public function getTimeLocal()
    {
        $t = '';
    }

    public function setTimeLocal($value)
    {
        $t = '';
    }

    public function __set($name, $value)
    {
        parent::__set($name, $value);
    }
}