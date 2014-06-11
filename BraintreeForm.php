<?php
namespace tuyakhov\braintree;

use yii\base\Model;
use yii\base\ModelEvent;

class BraintreeForm extends Model
{

    public $amount;
    public $orderId;
    public $paymentMethodToken;

    public $creditCard_number;
    public $creditCard_cvv;
    public $creditCard_expirationMonth;
    public $creditCard_expirationYear;
    public $creditCard_expirationDate;
    public $creditCard_name;

    public $customer_firstName;
    public $customer_lastName;
    public $customer_company;
    public $customer_phone;
    public $customer_fax;
    public $customer_website;
    public $customer_email;

    public $billing_firstName;
    public $billing_lastName;
    public $billing_company;
    public $billing_streetAddress;
    public $billing_extendedAddress;
    public $billing_locality;
    public $billing_region;
    public $billing_postalCode;
    public $billing_countryCodeAlpha2;

    public $shipping_firstName;
    public $shipping_lastName;
    public $shipping_company;
    public $shipping_streetAddress;
    public $shipping_extendedAddress;
    public $shipping_locality;
    public $shipping_region;
    public $shipping_postalCode;
    public $shipping_countryCodeAlpha2;

    public $customerId;

    public function rules()
    {
        return [
            [['customerId', 'creditCard_number', 'creditCard_cvv', 'creditCard_expirationDate'], 'required', 'on' => 'creditCard'],
            [['customerId'], 'required', 'on' => 'address'],
            [['customer_firstName', 'customer_lastName'], 'required', 'on' => 'customer'],
            [['amount', 'creditCard_number', 'creditCard_cvv', 'creditCard_expirationDate'], 'required', 'on' => 'sale'],
            [['amount', 'paymentMethodToken'], 'required', 'on' => 'saleFromVault'],
            [['amount'], 'double'],
            [['customer_email'], 'email'],
            [['creditCard_expirationMonth',
                'creditCard_expirationYear',
                'creditCard_expirationDate',
                'customer_firstName',
                'customer_lastName',
                'customer_company',
                'customer_phone',
                'customer_fax',
                'customer_website',
                'billing_firstName',
                'billing_lastName',
                'billing_company',
                'billing_streetAddress',
                'billing_extendedAddress',
                'billing_locality',
                'billing_region',
                'billing_postalCode',
                'billing_countryCodeAlpha2',
                'shipping_firstName',
                'shipping_lastName',
                'shipping_company',
                'shipping_streetAddress',
                'shipping_extendedAddress',
                'shipping_locality',
                'shipping_region',
                'shipping_postalCode',
                'shipping_countryCodeAlpha2'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'amount' => 'Amount($)',
            'orderId' => 'Order ID',
            'creditCard_number' => 'Credit Card Number',
            'creditCard_cvv' => 'Security Code',
            'creditCard_expirationMonth' => 'Expiration Month (MM)',
            'creditCard_expirationYear' => 'Expiration Year (YYYY)',
            'creditCard_expirationDate' => 'Expiration Date (MM/YYYY)',
            'creditCard_name' => 'Name on Card',
            'customer_firstName' => 'First Name',
            'customer_lastName' => 'Last Name',
            'customer_company' => 'Company Name',
            'customer_phone' => 'Phone Number',
            'customer_fax' => 'Fax Number',
            'customer_website' => 'Website',
            'customer_email' => 'Email',
            'billing_firstName' => 'First Name',
            'billing_lastName' => 'Last Name',
            'billing_company' => 'Company Name',
            'billing_streetAddress' => 'Address',
            'billing_extendedAddress' => 'Address',
            'billing_locality' => 'City/Locality',
            'billing_region' => 'State/Region',
            'billing_postalCode' => 'Zip/Postal Code',
            'billing_countryCodeAlpha2' => 'Country',
            'shipping_firstName' => 'First Name',
            'shipping_lastName' => 'Last Name',
            'shipping_company' => 'Company Name',
            'shipping_streetAddress' => 'Address',
            'shipping_extendedAddress' => 'Address',
            'shipping_locality' => 'City/Locality',
            'shipping_region' => 'State/Region',
            'shipping_postalCode' => 'Zip/Postal Code',
            'shipping_countryCodeAlpha2' => 'Country',
        ];
    }

    public function getValuesFromAttributes()
    {
        $values = array();
        foreach ($this->attributes as $key => $val) {
            if (!is_object($val) && !is_null($val) && strlen($val) > 0) {
                if (strpos($key, '_') === false) {
                    $values[$key] = $val;
                } else {
                    $pieces = explode('_', $key);
                    $values[$pieces[0]][$pieces[1]] = $val;
                }
            }
        }
        return $values;
    }

    public function send()
    {
        \Yii::$app->get('braintree')->getOptions($this->getValuesFromAttributes());
        $scenario = $this->getScenario();
        return $this->$scenario();

    }

    public function sale()
    {
        $return = \Yii::$app->get('braintree')->singleCharge();
        if ($return['status'] === false) {
            $this->addErrorFromResponse($return['result']);
            return false;
        } else {
            return $return;
        }
    }

    public function saleFromVault()
    {
        $return = \Yii::$app->get('braintree')->singleCharge();
        if ($return['status'] === false) {
            $this->addErrorFromResponse($return['result']);
            return false;
        } else {
            return $return;
        }
    }

    public function customer()
    {
        $return = \Yii::$app->get('braintree')->saveCustomer();
        if ($return['status'] === false) {
            $this->addErrorFromResponse($return['result']);
            return false;
        } else {
            return $return;
        }
    }

    public function creditCard()
    {
        $return = \Yii::$app->get('braintree')->saveCreditCard();
        if ($return['status'] === false) {
            $this->addErrorFromResponse($return['result']);
            return false;
        } else {
            return $return;
        }
    }

    public function address()
    {
        $return = \Yii::$app->get('braintree')->saveAddress();
        if ($return['status'] === false) {
            $this->addErrorFromResponse($return['result']);
            return false;
        } else {
            return $return;
        }
    }

    /**
     * This add error from braintree response.
     * @param $result \Braintree_Result_Error
     */
    public function addErrorFromResponse($result)
    {
        $values = $this->getValuesFromAttributes();
        if (!empty($result->errors)) {
            foreach (array_keys($values) as $key) {
                $keyErrors = $result->errors->forKey($key);
                if (isset($keyErrors)) {
                    foreach ($keyErrors->shallowAll() as $error) {
                        $this->addError(($key . '_' . $error->attribute), $error->message);
                    }
                }
            }
        }
    }

}