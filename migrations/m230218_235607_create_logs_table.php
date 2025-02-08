<?php

namespace aigletter\logging\migrations;

//use yii\db\Migration;
//use bashkarev\clickhouse\Migration;
use yii\db\Migration;
/**
 * Handles the creation of table `{{%log}}`.
 */
class m230218_235607_create_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    { 
        $this->createTable('{{%logs}}', [
            'id' => $this->string(),
            'remoteAddr' => $this->string(),
            'remoteUser' => $this->string(),
            'timeLocal' => $this->timestamp(),
            'request' => $this->text(),
            'status' => $this->integer(),
            'bodyBytesSent' => $this->integer(),
            'httpReferer' => $this->text(),
            'httpUserAgent' => $this->string(),
            // VARCHAR тому що в Memory не підтримуються тип TEXT
            'origin' => $this->text(),
            'PRIMARY KEY (id)'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%logs}}');
    }
}
