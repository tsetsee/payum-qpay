<?php

namespace Tsetsee\PayumQPay;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Payum\Core\Storage\StorageInterface;
use Tsetsee\PayumQPay\Action\Api\CheckPaymentAction;
use Tsetsee\PayumQPay\Action\Api\CreateInvoiceAction;
use Tsetsee\PayumQPay\Action\CaptureAction;
use Tsetsee\PayumQPay\Action\ConvertPaymentAction;
use Tsetsee\PayumQPay\Action\ConvertQPayAction;
use Tsetsee\PayumQPay\Action\NotifyAction;
use Tsetsee\PayumQPay\Action\StatusAction;
use Tsetsee\PayumQPay\Action\SyncAction;
use Tsetsee\Qpay\Api\Enum\Env;
use Webmozart\Assert\Assert;

class PayumQPayGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $notifyAction = new NotifyAction();

        $config->defaults([
            'payum.factory_name' => 'qpay',
            'payum.factory_title' => 'QPay',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => $notifyAction,
            'payum.action.status' => new StatusAction(),
            'payum.action.sync' => new SyncAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.convert_qpay' => new ConvertQPayAction(),
            'payum.action.check_payment' => new CheckPaymentAction(),
            'payum.action.create_invoice' => new CreateInvoiceAction(),
        ]);

        if ($config['storage'] instanceof StorageInterface) {
            $notifyAction->setStorage($config['storage']);
        }

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandbox' => true,
                'username' => '',
                'password' => '',
                'options' => [],
            ];
            /* @phpstan-ignore-next-line */
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty(['username', 'password', 'invoiceCode']);

                Assert::string($config['username']);
                Assert::string($config['password']);
                Assert::boolean($config['sandbox']);
                Assert::string($config['invoiceCode']);

                $api = new Api(
                    $config['username'],
                    $config['password'],
                    $config['sandbox'] ? Env::SANDBOX : Env::PROD,
                    $config['invoiceCode']
                );

                /** @var ?array<string, mixed> $options */
                $options = $config['options'];
                $api->setup($options ?? []);

                return $api;
            };
        }
    }
}
