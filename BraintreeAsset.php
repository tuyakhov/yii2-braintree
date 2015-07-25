<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use yii\web\AssetBundle;

class BraintreeAsset extends AssetBundle
{
    public $sourcePath = '@npm/braintree-web/dist';
    public $js = [
        'braintree.js',
    ];

}