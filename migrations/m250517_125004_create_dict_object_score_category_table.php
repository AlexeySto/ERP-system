<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%dict_object_score_category}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%dict_object_score_section}}`
 * - `{{%users}}`
 * - `{{%users}}`
 */
class m250517_125004_create_dict_object_score_category_table extends Migration
{
 /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%dict_object_score_category}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->Null()->comment('Название категории'),
            'description' => $this->string(255)->Null()->comment('Описание'),
            'importance_life' => $this->tinyInteger()->Null()->defaultValue(10)->comment('Важность для жизни'),
            'importance_investment' => $this->tinyInteger()->Null()->defaultValue(10)->comment('Важность для инвестиций'),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0)->comment('Запись удалена'),
            'created' => $this->dateTime()->notNull()->defaultExpression('NOW()')->comment('Дата создания'),
            'creator_id' => $this->integer()->comment('Создал'),
            'modified' => $this->dateTime()->comment('Дата изменения'),
            'modifier_id' => $this->integer()->comment('Изменил'),
            'section_id' => $this->integer()->notNull()->comment('ID раздела'),
            'sort_order' => $this->integer()->unsigned()->comment('Порядок сортировки'),
        ]);

        // creates index for column `creator_id`
        $this->createIndex(
            '{{%idx-dict_object_score_category-creator_id}}',
            '{{%dict_object_score_category}}',
            'creator_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-dict_object_score_category-creator_id}}',
            '{{%dict_object_score_category}}',
            'creator_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `modifier_id`
        $this->createIndex(
            '{{%idx-dict_object_score_category-modifier_id}}',
            '{{%dict_object_score_category}}',
            'modifier_id'
        );

        // add foreign key for table `{{%users}}`
        $this->addForeignKey(
            '{{%fk-dict_object_score_category-modifier_id}}',
            '{{%dict_object_score_category}}',
            'modifier_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `section_id`
        $this->createIndex(
            '{{%idx-dict_object_score_category-section_id}}',
            '{{%dict_object_score_category}}',
            'section_id'
        );

        // add foreign key for table `{{%dict_object_score_section}}`
        $this->addForeignKey(
            '{{%fk-dict_object_score_category-section_id}}',
            '{{%dict_object_score_category}}',
            'section_id',
            '{{%dict_object_score_section}}',
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
            '{{%fk-dict_object_score_category-creator_id}}',
            '{{%dict_object_score_category}}'
        );

        // drops index for column `creator_id`
        $this->dropIndex(
            '{{%idx-dict_object_score_category-creator_id}}',
            '{{%dict_object_score_category}}'
        );

        // drops foreign key for table `{{%users}}`
        $this->dropForeignKey(
            '{{%fk-dict_object_score_category-modifier_id}}',
            '{{%dict_object_score_category}}'
        );

        // drops index for column `modifier_id`
        $this->dropIndex(
            '{{%idx-dict_object_score_category-modifier_id}}',
            '{{%dict_object_score_category}}'
        );

        // drops foreign key for table `{{%dict_object_score_section}}`
        $this->dropForeignKey(
            '{{%fk-dict_object_score_category-section_id}}',
            '{{%dict_object_score_category}}'
        );

        // drops index for column `section_id`
        $this->dropIndex(
            '{{%idx-dict_object_score_category-section_id}}',
            '{{%dict_object_score_category}}'
        );

        $this->dropTable('{{%dict_object_score_category}}');
    }
}
