<?php
/**
 * @copyright Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace tuyakhov\braintree;

use yii\base\Component;
use yii\base\InvalidConfigException;

class Braintree extends Component
{
    public $environment = 'sandbox';
    public $merchantId;
    public $publicKey;
    public $privateKey;
    public $clientSideKey;

    public $options;

    /**
     * Sets up Braintree configuration from config file
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        foreach (['merchantId', 'publicKey', 'privateKey', 'environment'] as $attribute) {
            if ($this->$attribute === null) {
                throw new InvalidConfigException(strtr('"{class}::{attribute}" cannot be empty.', [
                    '{class}' => static::className(),
                    '{attribute}' => '$' . $attribute
                ]));
            }
            \Braintree_Configuration::$attribute($this->$attribute);
        }
        parent::init();
    }

    /**
     * Braintree sale function
     */
    public function singleCharge($submitForSettlement = true, $storeInVaultOnSuccess = true)
    {
        $this->options['options']['submitForSettlement'] = $submitForSettlement;
        $this->options['options']['storeInVaultOnSuccess'] = $storeInVaultOnSuccess;
        $result = \Braintree_Transaction::sale($this->options);

        if ($result->success) {
            return ['status' => true, 'result' => $result];
        } else if ($result->transaction) {
            return ['status' => false, 'result' => $result];
        } else {
            return ['status' => false, 'result' => $result];
        }
    }

    /**
     * This save customer to braintree and returns result array
     */
    public function saveCustomer()
    {
        $result = \Braintree_Customer::create($this->options['customer']);

        if ($result->success) {
            return ['status' => true, 'result' => $result];
        } else {
            return ['status' => false, 'result' => $result];
        }
    }

    /**
     * This save credit cart to braintree
     */
    public function saveCreditCard()
    {
        $send_array = $this->options['creditCard'];
        if (isset($this->options['billing'])) {
            $send_array['billingAddress'] = $this->options['billing'];
        }
        if (isset($this->options['customerId'])) {
            $send_array['customerId'] = $this->options['customerId'];
        }
        $result = \Braintree_CreditCard::create($send_array);

        if ($result->success) {
            return ['status' => true, 'result' => $result];
        } else {
            return ['status' => false, 'result' => $result];
        }
    }

    public function saveAddress()
    {
        $send_array = $this->options['billing'];
        if (isset($this->options['customerId'])) {
            $send_array['customerId'] = $this->options['customerId'];
        }
        $result = \Braintree_Address::create($send_array);

        if ($result->success) {
            return ['status' => true, 'result' => $result];
        } else {
            return ['status' => false, 'result' => $result];
        }
    }

    /**
     * Constructs the Credit Card array for payment
     * @param integer $number Credit Card Number
     * @param integer $cvv (optional)Credit Card Security code
     * @param integer $expirationMonth format: MM (use expirationMonth and expirationYear or expirationDate not both)
     * @param integer $expirationYear format: YYYY (use expirationMonth and expirationYear or expirationDate not both)
     * @param string $expirationDate format: MM/YYYY (use expirationMonth and expirationYear or expirationDate not both)
     */
    public function setCreditCard($number, $cvv = null, $expirationMonth = null, $expirationYear = null, $expirationDate = null)
    {
        $this->options['creditCard'] = [];
        $this->options['creditCard']['number'] = $number;
        if (isset($cvv)) $this->options['creditCard']['cvv'] = $cvv;
        if (isset($expirationMonth)) $this->options['creditCard']['expirationMonth'] = $expirationMonth;
        if (isset($expirationYear)) $this->options['creditCard']['expirationYear'] = $expirationYear;
        if (isset($expirationDate)) $this->options['creditCard']['expirationDate'] = $expirationDate;
    }

    public function getCreditCard($input_values)
    {
        $default = [
            'cvv' => null,
            'month' => null,
            'year' => null,
            'date' => null,
            'name' => null,
        ];
        $values = array_merge($default, $input_values);
        $this->setCreditCard($values['number'], $values['cvv'], $values['month'], $values['year'], $values['date'], $values['name']);
    }

    public function getOptions($values)
    {
        if (isset($values['amount'])) $this->setAmount($values['amount']);
        if (isset($values['orderId'])) $this->options['orderId'] = $values['orderId'];
        if (isset($values['creditCard'])) $this->getCreditCard($values['creditCard']);
        if (isset($values['customer'])) $this->options['customer'] = $values['customer'];
        if (isset($values['billing'])) $this->options['billing'] = $values['billing'];
        if (isset($values['shipping'])) $this->options['shipping'] = $values['shipping'];

        //For storing payment method in vault
        if (isset($values['customerId'])) $this->options['customerId'] = $values['customerId'];
    }

    /**
     * Set the amount to charge
     * @param float $amount No dollar sign needed
     */
    public function setAmount($amount)
    {
        $this->options['amount'] = round($amount, 2);
    }
}