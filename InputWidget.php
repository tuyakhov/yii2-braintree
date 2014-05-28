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
        if ($this->hasModel()) {
            if (strpos($this->attribute, '_') === false) {
                $value = $this->attribute;
            } else {
                $pieces = explode('_', $this->attribute);
                $value = $pieces[1];
            }
            $this->options['data-encrypted-name'] = $value;
        }
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
            $content = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $content = Html::textInput($this->name, $this->value, $this->options);
        }
        return $content;
    }
}