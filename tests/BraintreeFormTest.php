<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\braintree\tests;


use tuyakhov\braintree\BraintreeForm;

class BraintreeFormTest extends TestCase
{

    /**
     * @dataProvider validCreditCardProvider
     */
    public function testSingleCharge($creditCard)
    {
        $model = new BraintreeForm();
        $model->setScenario('sale');
        $this->assertTrue($model->load($creditCard, ''));
        $model->amount = rand(1, 200);
        $this->assertTrue($model->send());
    }

    public function validCreditCardProvider()
    {
        return [
            'creditCard_number' => '5555555555554444',
            'creditCard_cvv' => '123',
            'creditCard_expirationDate' => '12/2020'
        ];
    }

    public function customerProvider()
    {
        return [
            'customer_firstName' => 'Brad',
            'customer_lastName' => 'Pitt',
        ];
    }
}