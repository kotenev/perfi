<?php

namespace app\models;

use Yii;
use app\classes\Caption;
use app\classes\SetFlashCRUD;

/**
 * This is the model class for table "{{%income_category}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $account_id
 *
 * @property Income[] $incomes
 * @property Account $account
 * @property User $user
 */
class IncomeCategory extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%income_category}}';
    }

    public function behaviors() {
        return [
            // Сообщения действий CRUD
            SetFlashCRUD::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'account_id', 'user_id'], 'integer'],
            [['name', 'account_id', 'user_id'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name', 'user_id'], 'unique', 'targetAttribute' => ['name', 'user_id'], 'message' => Caption::VALIDATION_INCOME_CATEGORY_UNIQUE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'name' => 'Наименование',
            'account_id' => 'Счет по умолчанию',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomes() {
        return $this->hasMany(Income::className(), ['income_category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount() {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Возвращает список Пользователей и их Категорий
     */
    public static function findAllAndUserName() {
        if (Yii::$app->user->can('show_all')) {
            $sql = 'SELECT  a.id as id, concat(a.name, " (", us.username, ")") as name
            FROM {{%income_category}} a, db1_user us
            where a.user_id = us.id  order by us.username, a.name';
        } else {
            $sql = 'SELECT a.id as id, a.name FROM {{%income_category}} a, db1_user us
            where a.user_id = us.id
            and a.user_id = ' . Yii::$app->user->identity->id . ' order by us.username, a.name';
        }
        return self::findBySql($sql)->all();
    }

}
