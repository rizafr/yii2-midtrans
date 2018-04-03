<?php
/**
 * Created by PhpStorm.
 * User: Butterfly
 * Date: 3/26/2018
 * Time: 2:49 PM
 */

namespace chabibnr\midtrans\actions;

use chabibnr\midtrans\events\MidtransEvent;
use chabibnr\midtrans\models\MidtransData;
use chabibnr\midtrans\models\MidtransRequest;
use Yii;
use chabibnr\midtrans\components\Midtrans;
use yii\base\Action;
use yii\base\Event;
use yii\db\Exception;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\web\Response;

class StatusAction extends Action {

    public function run($orderId, $redirect = '') {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        /** @var Midtrans $midtrans */
        $midtrans = Yii::$app->midtrans;
        $client = new Client(['baseUrl' => $midtrans->getBaseApiUrl()]);
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('/' . $orderId . '/status')
            ->addHeaders(['content-type' => 'application/json'])
            ->addHeaders(['accept' => 'application/json'])
            ->addHeaders(['Authorization' => 'Basic ' . $midtrans->getAuthorize()])
            ->send();

        if ($response->isOk && is_array($response->data) && isset($response->data['order_id'])) {
            $data = (object)$response->data;

            $generateSignature = $data->order_id;
            $generateSignature .= $data->status_code;
            $generateSignature .= $data->gross_amount;
            $generateSignature .= $midtrans->serverKey;

            if ((openssl_digest($generateSignature, 'sha512') == $data->signature_key)/* AND (in_array($data->transaction_status, ['settlement', 'pending', 'cancel']))*/) {
                $transaction = Yii::$app->getDb()->beginTransaction();
                try {
                    //check on PreData
                    $midtransRequest = MidtransRequest::findOne(['order_id' => $orderId]);

                    if ($midtransRequest != null) {
                        $model = new MidtransData();
                        $model->user_id = $midtransRequest->user_id;
                        $model->transaction_id = $data->transaction_id;
                        $model->order_id = $data->order_id;
                        $model->transaction_time = $data->transaction_time;
                        $model->payment_type = $data->payment_type;
                        $model->additional = $midtransRequest->additional;
                        if(!$midtransRequest->delete()){
                            throw  new Exception("Error Deleting request");
                        }
                    } else {
                        $model = MidtransData::findOne(['order_id' => $orderId]);
                    }
                    if (!in_array($model->transaction_status, $midtrans->paidStatus)) {
                        $model->transaction_status = $data->transaction_status;
                        $model->fraud_status = isset($data->fraud_status) ? $data->fraud_status : '';
                        $model->approval_code = isset($data->approval_code) ? $data->approval_code : '';
                        $model->signature = $data->signature_key;
                        $model->bank = isset($data->bank) ? $data->bank : '';
                        $model->gross_amount = $data->gross_amount;
                        if ($model->save()) {
                            //todo trigger after update
                            $event = new MidtransEvent();
                            $event->midtransData = $model;
                            $midtrans->trigger(Midtrans::EVENT_STATUS_CHANGE, $event);
                        }else{
                            throw new Exception("Gagal menyimpan data dari Midtrans");
                        }
                        $transaction->commit();
                    }

                    return [
                        'status' => 'ok',
                        'message' => 'Transaksi dengan order ID '. $data->order_id .' Status '. $data->transaction_status
                    ];

                } catch (Exception $exception) {
                    $transaction->rollBack();
                    Yii::error($exception->getMessage());
                    return [
                        'status' => 'error',
                        'message' => $exception->getMessage()
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Transaksi tidak diketahui'
                ];
            }

        }else{
            return [
                'status' => 'error',
                'message' => $response->data['status_message']
            ];
        }
    }
}