<?php
/**
 * Created by PhpStorm.
 * User: Butterfly
 * Date: 3/24/2018
 * Time: 3:25 PM
 */

namespace chabibnr\midtrans\components;


use yii\base\BaseObject;
use yii\base\Component;
use yii\helpers\Url;

/**
 * Class Midtrans
 * @package chabibnr\midtrans\components
 * @property string tokenUri
 */
class Midtrans extends Component {

    const EVENT_STATUS_CHANGE = 'status-change';

    public $isProduction = false;
    public $serverKey;
    public $clientKey;
    public $checkoutUri;
    public $statusUri;
    public $paidStatus = ['capture','settlement'];

    public function getTokenUri(){
        if(is_array($this->checkoutUri)){
            $this->checkoutUri = Url::to($this->checkoutUri);
        }
        return $this->checkoutUri;
    }

    public function getBaseUrl(){
        if($this->isProduction){
            return 'https://app.sandbox.midtrans.com';
        }else{
            return 'https://app.sandbox.midtrans.com';
        }
    }

    public function getBaseApiUrl(){
        if($this->isProduction){
            return 'https://api.midtrans.com/v2';
        }else{
            return 'https://api.sandbox.midtrans.com/v2';
        }
    }

    public function getAuthorize(){
        return 'Basic '. base64_encode($this->serverKey.':');
    }

}