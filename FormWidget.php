<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use yii\base\Widget;
use Yii;
use yii\helpers\Html;

class FormWidget extends Widget
{
    /**
     * @param array|string $action the form action URL. This parameter will be processed by [[\yii\helpers\Url::to()]].
     */
    public $action = '';
    /**
     * @var string the form submission method. This should be either 'post' or 'get'. Defaults to 'post'.
     *
     *
     * ```php
     * FormWidget::begin([
     *     'method' => 'get',
     *     'action' => ['controller/action'],
     * ]);
     * ```
     */
    public $method = 'post';
    /**
     * @var array the HTML attributes (name-value pairs) for the form tag.
     */
    public $options = [];
    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        echo Html::beginForm($this->action, $this->method, $this->options);
        parent::init();
    }
    /**
     * Runs the widget.
     * This registers the necessary for encryption javascript code and renders the form close tag.
     */
    public function run()
    {
        $id = $this->options['id'];
        $clientSideKey = Yii::$app->get('braintree')->clientSideKey;
        $view = $this->getView();
        BraintreeAsset::register($view);
        $view->registerJs("braintree.setup('$clientSideKey', 'custom', {id: '$id'});");
        echo Html::endForm();
    }


}