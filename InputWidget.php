<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use yii\helpers\Html;

class InputWidget extends \yii\widgets\InputWidget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options['data-encrypted-name'] = Html::getInputName($this->model, $this->attribute);
        $this->options['autocomplete'] = 'off';
        echo $this->renderWidget();
        parent::init();
    }

    /**
     * Renders input
     * @return string
     */
    public function renderWidget()
    {
        if ($this->hasModel()) {
            $this->options['value'] = '';
            $content = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $content = Html::textInput($this->name, '', $this->options);
        }
        return $content;
    }
}