<?php

namespace Tsetsee\PayumQPay;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Tsetsee\PayumQPay\Action\AuthorizeAction;
use Tsetsee\PayumQPay\Action\CancelAction;
use Tsetsee\PayumQPay\Action\CaptureAction;
use Tsetsee\PayumQPay\Action\ConvertPaymentAction;
use Tsetsee\PayumQPay\Action\NotifyAction;
use Tsetsee\PayumQPay\Action\RefundAction;
use Tsetsee\PayumQPay\Action\StatusAction;
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
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
                'options' => [],
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.qpay.username']);
                $config->validateNotEmpty($config['payum.qpay.password']);
                $config->validateNotEmpty($config['payum.qpay.password']);


                return new QPayApi(
                    $config['payum.qpay.username'],
                    $config['payum.qpay.password'],
                    $config['payum.default_options']['sandbox'] ? Env::SANDBOX : Env::PROD,
                    $config['options']
                );
            };
        }
    }
}
