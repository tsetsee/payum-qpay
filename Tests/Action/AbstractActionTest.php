<?php

namespace Tests\Action;

use Payum\Core\GatewayInterface;
use Payum\Core\Security\TokenInterface;
use Tsetsee\PayumQPay\Api;
use PHPUnit\Framework\MockObject\MockObject;
use Payum\Core\Tests\GenericActionTest;

/**
 * Class AbstractActionTest
 */
abstract class AbstractActionTest extends GenericActionTest
{
    /**
     * @return MockObject&GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->getMockBuilder(GatewayInterface::class)->getMock();
    }

    /**
     * @return MockObject&TokenInterface
     */
    protected function createTokenMock()
    {
        return $this->getMockBuilder(TokenInterface::class)->getMock();
    }

    /**
     * @return MockObject&Api
     */
    protected function createApiMock()
    {
        return $this->getMockBuilder(Api::class)->getMock();
    }
}
