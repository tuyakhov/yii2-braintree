<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use Yii;

class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $id = $this->options['id'];
        $clientSideKey = Yii::$app->get('braintree')->clientSideKey;
        $view = $this->getView();
        BraintreeAsset::register($view);
        $view->registerJs("braintree.setup('$clientSideKey', 'custom', {id: '$id'});");
        $this->fieldClass = ActiveField::className();
    }

}