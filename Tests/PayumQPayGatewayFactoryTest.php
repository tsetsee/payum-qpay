<?php

namespace Tests;

use Payum\Core\Tests\AbstractGatewayFactoryTest;
use Tsetsee\PayumQPay\PayumQPayGatewayFactory;

class PayumQPayGatewayFactoryTest extends AbstractGatewayFactoryTest
{
    public function testShouldAddDefaultConfigPassedInConstructorWhileCreatingGatewayConfig(): void
    {
        $factory = new PayumQPayGatewayFactory([
            'foo' => 'fooVal',
            'bar' => 'barVal',
        ]);

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('foo', $config);
        $this->assertSame('fooVal', $config['foo']);

        $this->assertArrayHasKey('bar', $config);
        $this->assertSame('barVal', $config['bar']);
    }

    public function testShouldConfigContainDefaultOptions(): void
    {
        $factory = new PayumQPayGatewayFactory();

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('payum.default_options', $config);
        $this->assertEquals(
            [
                'username' => '',
                'password' => '',
                'options' => [],
                'sandbox' => true,
            ],
            $config['payum.default_options']
        );
    }

    public function testShouldConfigContainFactoryNameAndTitle(): void
    {
        $factory = new PayumQPayGatewayFactory();

        $config = $factory->createConfig();

        $this->assertIsArray($config);

        $this->assertArrayHasKey('payum.factory_name', $config);
        $this->assertSame('qpay', $config['payum.factory_name']);

        $this->assertArrayHasKey('payum.factory_title', $config);
        $this->assertSame('QPay', $config['payum.factory_title']);
    }

    public function testShouldThrowIfRequiredOptionsNotPassed(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The username, password fields are required.');
        $factory = new PayumQPayGatewayFactory();

        $factory->create();
    }

    protected function getGatewayFactoryClass(): string
    {
        return PayumQPayGatewayFactory::class;
    }

    protected function getRequiredOptions(): array
    {
        return [
            'sandbox' => true,
            'username' => 'test',
            'password' => 'pass1',
            'options' => [],
        ];
    }
}
