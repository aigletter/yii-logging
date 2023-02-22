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
        return \Yii::$app->getModule('logging')?->getDb();
    }

    public function setOrigin($value)
    {
        $t = '';
    }
}