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
        $this->clientSideKey = \Braintree_ClientToken::generate();
        parent::init();
    }

    /**
     * Braintree sale function
     * @param bool|true $submitForSettlement
     * @param bool|true $storeInVaultOnSuccess
     * @return array
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
     * Finds transaction by id
     */
    public function findTransaction($id)
    {
        return \Braintree_Transaction::find($id);
    }

    /**
     * This save customer to braintree and returns result array
     * @return array
     */
    public function saveCustomer()
    {
        if (isset($this->options['customerId'])) {
            $this->options['customer']['id'] = $this->options['customerId'];
        }
        $result = \Braintree_Customer::create($this->options['customer']);

        if ($result->success) {
            return ['status' => true, 'result' => $result];
        } else {
            return ['status' => false, 'result' => $result];
        }
    }

    /**
     * This save credit cart to braintree
     * @return array
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
            'expirationMonth' => null,
            'expirationYear' => null,
            'expirationDate' => null,
            'name' => null,
        ];
        $values = array_merge($default, $input_values);
        $this->setCreditCard($values['number'], $values['cvv'], $values['expirationMonth'], $values['expirationYear'], $values['expirationDate'], $values['name']);
    }

    public function getOptions($values)
    {
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                if ($key == 'amount')
                    $this->setAmount($values['amount']);
                elseif ($key == 'creditCard')
                    $this->getCreditCard($values['creditCard']);
                else
                    $this->options[$key] = $value;
            }
        }
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