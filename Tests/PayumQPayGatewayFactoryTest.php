<?php

namespace Tests;

use Payum\Core\Tests\AbstractGatewayFactoryTest;
use Tsetsee\PayumQPay\PayumQPayGatewayFactory;

class PayumQPayGatewayFactoryTest extends AbstractGatewayFactoryTest
{
    public function testShouldAddDefaultConfigPassedInConstructorWhileCreatingGatewayConfig(): void
    {
        $factory = new PayumQPayGatewayFactory([
            'sandbox' => false,
            'payum.qpay.username' => 'test2',
            'payum.qpay.password' => 'pass2',
        ]);

        $config = $factory->createConfig();

        $this->assertIsArray($config);
    }

    protected function getGatewayFactoryClass(): string
    {
        return PayumQPayGatewayFactory::class;
    }

    protected function getRequiredOptions(): array
    {
        return [
            'sandbox' => true,
            'payum.qpay.username' => 'test',
            'payum.qpay.password' => 'pass1',
        ];
    }
}
