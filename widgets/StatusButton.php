<?php
/**
 * Created by PhpStorm.
 * User: Butterfly
 * Date: 3/29/2018
 * Time: 10:52 AM
 */

namespace chabibnr\midtrans\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use chabibnr\midtrans\components\Midtrans;

class StatusButton extends Widget {
    public $attributes = [];
    public $text = 'Cek Status';
    public $uri = '';
    public $orderId = '';

    public function run(){
        if(is_array($this->uri)){
            $this->uri = Url::to($this->uri);
        }

        if(empty($this->uri) && empty($this->orderId)){
            throw new InvalidConfigException("parameter uri dan orderId tidak boleh kosong");
        }

        /** @var Midtrans $midtrans */
        $midtrans = Yii::$app->midtrans;
        $view = Yii::$app->getView();

        $view->registerJs(new JsExpression($this->render('../../views/status.js',[
            'statusUri' => $this->uri,
            'orderId' => $this->orderId
        ])), View::POS_END);

        if(!isset($this->attributes['class'])){
            $this->attributes['class'] = '';
        }

        $this->attributes['data-order-id'] = $this->orderId;
        $this->attributes['class'] = $this->attributes['class'] .' action-midtrans-status';
        return Html::button($this->text,$this->attributes);
    }
}