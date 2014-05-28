<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use yii\web\AssetBundle;

class BraintreeAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tuyakhov/yii2-braintree/assets';
    public $js = [
        'braintree.js',
    ];

}