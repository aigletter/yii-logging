<?php

namespace aigletter\logging\infrastructure\models;

use yii\db\ActiveRecord;

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
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{logs}}';
    }

    /**
     * @return \yii\db\Connection|null
     */
    public static function getDb()
    {
        return \Yii::$app->getModule('logging')?->getDb();
    }
}