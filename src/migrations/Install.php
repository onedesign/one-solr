<?php
namespace onedesign\onesolr\migrations;

use craft\db\Migration;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%onesolr_mappingpaths}}', [
            'id' => $this->primaryKey(),
            'sectionId' => $this->integer()->notNull(),
            'mappingPath' => $this->string()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%onesolr_mappingpaths}}');
    }
}