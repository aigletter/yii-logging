<?php

namespace aigletter\logging\models;

use yii\base\UnknownPropertyException;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

/**
 * @property $id
 * @property $remoteAddr;
 * @property $remoteUser;
 * @property $timeLocal;
 * @property $request;
 * @property $status;
 * @property $bodyBytesSent;
 * @property $httpReferer;
 * @property $httpUserAgent;
 * @property $origin
 */
class Log extends ActiveRecord
{
    public static function getDb()
    {
        $db = \Yii::$app->getModule('logging')?->db;

        return \Yii::$app->get($db);
    }

    public function setOrigin($value)
    {
        $t = '';
    }
}