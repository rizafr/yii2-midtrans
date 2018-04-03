<?php

namespace chabibnr\midtrans\assets;

use yii\web\AssetBundle;


class MidtransSnapCoreAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web/vue/dist';
    public $css = [];
    public $js = [
        'vue.min.js'
    ];
    public $depends = [];
}
