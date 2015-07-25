Yii2-braintree
==============

Integrate a credit card payment form with Braintree's API into Yii2. Inspired by


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tuyakhov/yii2-braintree "*"
```

or add

```
"tuyakhov/yii2-braintree": "*"
```

to the require section of your `composer.json` file.


Usage
-----

You should add Braintree component to your yii config first:

```
'components' => [
    'braintree' => [
        'class' => 'tuyakhov\braintree\Braintree',
        'merchantId' => 'YOUR_MERCHANT_ID',
        'publicKey' => 'YOUR_PUBLIC_KEY',
        'privateKey' => 'YOUR_PRIVATE_KEY',
    ],
]
```

Once the extension is installed, simply use it in your code by  :

`BraintreeForm` provide all basic operations for sales and stores customer info. Operation name equals scenario name. Available scenarios:

`creditCard` - create a credit card [doc](https://developers.braintreepayments.com/ios+php/reference/request/credit-card/create)  
`address` - create an address [doc](https://developers.braintreepayments.com/ios+php/reference/request/address/create)  
`customer` - create a customer [doc](https://developers.braintreepayments.com/ios+php/reference/request/customer/create)  
`sale` - create a transaction [doc](https://developers.braintreepayments.com/ios+php/reference/request/transaction/sale)  
`saleFromVault` - create a transaction from your Vault [doc](https://developers.braintreepayments.com/ios+php/reference/request/transaction/sale)  

Action example:
```php
public function actionSale() {
    $model = new BraintreeForm();
    $model->setScenario('sale');
    if ($model->load(Yii::$app->request->post()) && $model->send()) {
        // do something
    }
    return $this->render('purchase', ['model' => $model]);
}
```

Form widget for your view:
```php
    <?php $form = \tuyakhov\braintree\ActiveForm::begin() ?>
    <?= $form->field($model, 'creditCard_number'); ?>
    <?= $form->field($model, 'creditCard_cvv'); ?>
    <?= $form->field($model, 'creditCard_expirationDate')->widget(\yii\widgets\MaskedInput::className(), [
           'mask' => '99/9999',
       ]) ?>
    <?= $form->field($model, 'amount'); ?>
    <?= \yii\helpers\Html::submitButton()?>
    <?php \tuyakhov\braintree\ActiveForm::end(); ?>
```

