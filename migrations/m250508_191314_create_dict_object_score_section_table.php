<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dict_object_score_section}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%users}}`
 * - `{{%users}}`
 */
class m250508_191314_create_dict_object_score_section_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dict_object_score_section}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->comment('Название раздела'),
            'description' => $this->string(255)->Null()->comment('Описание'),
            'importance_life' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('Важность для жизни'),
            'importance_investment' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('Важность для инвестиций'),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0)->comment('Запись удалена'),
            'created' => $this->dateTime()->notNull()->defaultExpression('NOW()')->comment('Дата создания'),
            'creator_id' => $this->integer()->comment('Создал'),
            'modified' => $this->dateTime()->comment('Дата изменения'),
            'modifier_id' => $this->integer()->comment('Изменил'),
        ]);

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-dict_object_score_section-creator_id}}',
            '{{%dict_object_score_section}}',
            'creator_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-dict_object_score_section-creator_id}}',
            '{{%dict_object_score_section}}',
            'creator_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `modifier_id`
        $this->createIndex(
            '{{%idx-dict_object_score_section-modifier_id}}',
            '{{%dict_object_score_section}}',
            'modifier_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-dict_object_score_section-modifier_id}}',
            '{{%dict_object_score_section}}',
            'modifier_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-dict_object_score_section-creator_id}}',
            '{{%dict_object_score_section}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-dict_object_score_section-creator_id}}',
            '{{%dict_object_score_section}}'
        );

        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-dict_object_score_section-modifier_id}}',
            '{{%dict_object_score_section}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-dict_object_score_section-modifier_id}}',
            '{{%dict_object_score_section}}'
        );

        $this->dropTable('{{%dict_object_score_section}}');
    }
}
