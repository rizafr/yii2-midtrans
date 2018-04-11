<?php

namespace chabibnr\midtrans\widgets;

use chabibnr\midtrans\components\Midtrans;
use chabibnr\sweetalert\assets\SweetAlertAsset;
use chabibnr\sweetalert\widgets\SweetAlert;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class SnapButton extends Widget {

    public $tokenUri;
    public $text = "Checkout";
    public $attributes = [];

    public function run(){
        /** @var Midtrans $midtrans */
        $midtrans = Yii::$app->midtrans;

        $view = Yii::$app->getView();
        SweetAlertAsset::register($view);
        $view->registerJsFile($midtrans->getBaseUrl(). '/snap/snap.js',[
            'data-client-key' => $midtrans->clientKey
        ]);
        $view->registerJs(new JsExpression($this->render('../../views/snap.js',[
            'tokenUri' => $this->getTokenUri() !== null ? $this->getTokenUri() : $midtrans->getTokenUri(),
            'statusUri' => is_array($midtrans->statusUri) ? Url::to($midtrans->statusUri) : '',
        ])), View::POS_END);

        if(!isset($this->attributes['class'])){
            $this->attributes['class'] = '';
        }

        $this->attributes['class'] = $this->attributes['class'] .' action-midtrans-checkout';
        return Html::button($this->text,$this->attributes);
    }

    public function getTokenUri(){
        if(is_array($this->tokenUri)){
            return Url::to($this->tokenUri);
        }else{
            return $this->tokenUri;
        }
    }

    public function init(){
        parent::init();
    }
}