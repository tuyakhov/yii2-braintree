<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use Yii;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

class FormWidget extends ActiveForm
{
    public function init()
    {
        parent::init();
        $id = $this->options['id'];
        $clientSideKey = Yii::$app->get('braintree')->clientSideKey;
        $view = $this->getView();
        BraintreeAsset::register($view);
        $view->registerJs("braintree.setup('$clientSideKey', 'custom', {id: '$id'});");
        $this->fieldConfig = function ($model, $attribute) {
            return [
                'options' => [
                    'data-braintree-name' => Html::getInputName($model, $attribute),
                    'autocomplete' => 'off'
                ]
            ];
        };
    }

}