<?php

namespace chabibnr\midtrans\models;

use Yii;

/**
 * This is the model class for table "midtrans_request".
 *
 * @property int $id
 * @property string $user_id
 * @property string $order_id
 * @property string $data
 * @property string $additional
 */
class MidtransRequest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%midtrans_request}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['additional'], 'safe'],
            [['data'], 'string'],
            [['user_id', 'order_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'data' => 'Data',
        ];
    }

    public function beforeSave($insert) {
        $this->user_id = Yii::$app->getUser()->getId();
        return parent::beforeSave($insert);
    }
}