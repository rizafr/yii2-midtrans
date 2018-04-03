<?php
/**
 * Created by PhpStorm.
 * User: Butterfly
 * Date: 3/24/2018
 * Time: 3:21 PM
 */

namespace chabibnr\midtrans\actions;

use chabibnr\midtrans\components\Midtrans;
use chabibnr\midtrans\models\MidtransRequest;
use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\Response;

class CheckoutAction extends Action {

    public $orderId;
    public $grossAmount;

    public $itemDetails = [];
    public $customer_details = [];
    public $additional = [];
    public $passwordValidation = true;

    public function run($orderId = null, $grossAmount = 0, $itemDetails = [], $additional = null){

        if(!$this->passwordValidation){
            return [
                'status' => 'error',
                'message' => 'Password not match'
            ];
        }

        if(empty($this->orderId) && empty($orderId)){
            return [
                'status' => 'error',
                'message' => 'Order ID tidak ditemukan'
            ];
        }else{
            if(!empty($orderId)){
                $this->orderId = $orderId;
            }
        }

        if(empty($this->grossAmount) && empty($grossAmount)){
            return [
                'status' => 'error',
                'message' => 'Gross Amount tidak ditemukan'
            ];
        }else{
            if(!empty($grossAmount)){
                $this->grossAmount = $grossAmount;
            }
        }

        if(!empty($itemDetails)){
            $this->itemDetails = $itemDetails;
        }

        /** @var Midtrans $midtrans */
        $midtrans = Yii::$app->midtrans;

        $requestBody['transaction_details'] = [
            'order_id' => $this->orderId,
            'gross_amount' => $this->grossAmount
        ];

        $requestBody['customer_details'] = $this->customer_details;

        Yii::$app->response->format  = Response::FORMAT_JSON;
        $dataDetail = Json::encode($requestBody);

        MidtransRequest::deleteAll([
            'user_id' => Yii::$app->getUser()->getId(),
            'order_id' => $this->orderId
        ]);

        $model = new MidtransRequest();
        $model->order_id  = (string) $this->orderId;
        $model->data = $dataDetail;
        $model->additional = is_array($additional) ? serialize($additional) : $additional;
        if(!$model->save()){
            return [
                'status' => 'error',
                'message' => 'Tidak dapat menyimpan' . json_encode($model->errors)
            ];
        }

        $client = new Client(['baseUrl' => $midtrans->getBaseUrl()]);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('/snap/v1/transactions')
            ->addHeaders(['content-type' => 'application/json'])
            ->addHeaders(['accept' => 'application/json'])
            ->addHeaders(['Authorization' => $midtrans->getAuthorize()])
            ->setContent($dataDetail)
            ->send();

        if($response->isOk){
            $allResponse = $response->data;
            $allResponse['status'] = 'ok';
            return $allResponse;
        }else{
            Yii::error($response->data);
            return ['status' => 'error', 'message' => 'Gagal mendapatkan token'];
        }
    }
}