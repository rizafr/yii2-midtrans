<?php

namespace chabibnr\midtrans\models;


use Yii;

/**
 * This is the model class for table "midtrans_data".
 *
 * @property int $id
 * @property string $transaction_id
 * @property string $order_id
 * @property string $payment_type
 * @property string $transaction_time
 * @property string $transaction_status
 * @property string $fraud_status
 * @property string $approval_code
 * @property string $signature
 * @property string $bank
 * @property string $gross_amount
 * @property string $user_id
 * @property string $additional
 */
class MidtransData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'midtrans_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_time','additional'], 'safe'],
            [['signature'], 'string'],
            [['gross_amount'], 'number'],
            [['transaction_id', 'order_id', 'bank', 'user_id'], 'string', 'max' => 255],
            [['payment_type', 'transaction_status', 'fraud_status', 'approval_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'order_id' => 'Order ID',
            'payment_type' => 'Payment Type',
            'transaction_time' => 'Transaction Time',
            'transaction_status' => 'Transaction Status',
            'fraud_status' => 'Fraud Status',
            'approval_code' => 'Approval Code',
            'signature' => 'Signature',
            'bank' => 'Bank',
            'gross_amount' => 'Gross Amount',
            'user_id' => 'User ID',
        ];
    }
}