<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\braintree\tests;


use tuyakhov\braintree\BraintreeForm;

class BraintreeFormTest extends TestCase
{
    public static $customer;
    /**
     * @dataProvider validCreditCardProvider
     */
    public function testSingleCharge($ccNumber, $cvv, $exp)
    {
        $model = new BraintreeForm();
        $model->setScenario('sale');
        $this->assertTrue($model->load([
            'creditCard_number' => $ccNumber,
            'creditCard_cvv' => $cvv,
            'creditCard_expirationDate' => $exp
        ], ''));
        $model->amount = rand(1, 200);
        $this->assertNotFalse($model->send());
    }

    /**
     * @dataProvider customerProvider
     */
    public function testCustomerCreate($firstName, $lastName)
    {
        $model = new BraintreeForm();
        $model->setScenario('customer');
        $this->assertTrue($model->load([
            'customer_firstName' => $firstName,
            'customer_lastName' => $lastName,
        ], ''));
        $result = $model->send();
        $this->assertNotFalse($result);
        $this->assertArrayHasKey('result', $result);
        $this->assertObjectHasAttribute('customer', $result['result']);
        self::$customer = $result['result']->customer;
        $this->assertInstanceOf('\Braintree_Customer', self::$customer);
    }

    /**
     * @depends testCustomerCreate
     * @dataProvider validCreditCardProvider
     */
    public function testCreditCardCreate($ccNumber, $cvv, $exp)
    {
        $model = new BraintreeForm();
        $model->setScenario('creditCard');
        $this->assertTrue($model->load([
            'creditCard_number' => $ccNumber,
            'creditCard_cvv' => $cvv,
            'creditCard_expirationDate' => $exp
        ], ''));
        $model->customerId = self::$customer->id;
        $this->assertNotFalse($model->send());
    }

    /**
     * @depends testCustomerCreate
     */
    public function testTokenPayment()
    {
        $customer = \Braintree_Customer::find(self::$customer->id);
        $this->assertInstanceOf('\Braintree_Customer', $customer);
        $this->assertArrayHasKey(0, $customer->paymentMethods());
        $model = new BraintreeForm();
        $model->setScenario('saleFromVault');
        $this->assertTrue($model->load([
            'amount' => rand(1, 200),
            'paymentMethodToken' => $customer->paymentMethods()[0]->token
        ], ''));
        $this->assertNotFalse($model->send());
    }

    public function validCreditCardProvider()
    {
        return [
            [
                '5555555555554444',
                '123',
                '12/2020'
            ]
        ];
    }

    public function customerProvider()
    {
        return [
            [
                'Brad',
                'Pitt'
            ],
        ];
    }
}