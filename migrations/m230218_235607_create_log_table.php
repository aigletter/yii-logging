<?php

namespace aigletter\logging\migrations;

//use yii\db\Migration;
//use bashkarev\clickhouse\Migration;
use yii\db\Migration;
/**
 * Handles the creation of table `{{%log}}`.
 */
class m230218_235607_create_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    { 
        $this->createTable('{{%log}}', [
            'id' => $this->string(),
            'remoteAddr' => $this->string(),
            'remoteUser' => $this->string(),
            'timeLocal' => $this->timestamp(),
            'request' => $this->string(),
            'status' => $this->integer(),
            'bodyBytesSent' => $this->integer(),
            'httpReferer' => $this->string(),
            'httpUserAgent' => $this->string(),
            // VARCHAR тому що в Memory не підтримуються тип TEXT
            'origin' => $this->string(),
            //'PRIMARY KEY id'
            // Engine взяв такий, тому що він є як в MySQL так і в ClickHouse
        ], 'ENGINE = Memory');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%log}}');
    }
}
