<?php

namespace Tsetsee\PayumQPay;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Tsetsee\PayumQPay\Action\Api\CheckPaymentAction;
use Tsetsee\PayumQPay\Action\Api\CreateInvoiceAction;
use Tsetsee\PayumQPay\Action\CaptureAction;
use Tsetsee\PayumQPay\Action\ConvertPaymentAction;
use Tsetsee\PayumQPay\Action\ConvertQPayAction;
use Tsetsee\PayumQPay\Action\NotifyAction;
use Tsetsee\PayumQPay\Action\StatusAction;
use Tsetsee\PayumQPay\Action\SyncAction;
use Tsetsee\Qpay\Api\Enum\Env;
use Tsetsee\Qpay\Api\QPayApi;

class PayumQPayGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'qpay',
            'payum.factory_title' => 'QPay',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.sync' => new SyncAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.convert_qpay' => new ConvertQPayAction(),
            'payum.action.check_payment' => new CheckPaymentAction(),
            'payum.action.create_invoice' => new CreateInvoiceAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
                'username' => '',
                'password' => '',
                'options' => [],
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty(['username', 'password']);

                return new QPayApi(
                    $config['username'],
                    $config['password'],
                    $config['sandbox'] ? Env::SANDBOX : Env::PROD,
                    $config['options']
                );
            };
        }
    }
}
