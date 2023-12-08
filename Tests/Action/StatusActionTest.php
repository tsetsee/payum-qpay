<?php

namespace Tests\Action;

use Payum\Core\Request\GetHumanStatus;
use Tsetsee\PayumQPay\Action\StatusAction;
use Tsetsee\PayumQPay\Enum\PaymentStatus;

class StatusActionTest extends AbstractActionTest
{
    protected $requestClass = GetHumanStatus::class;
    protected $actionClass = StatusAction::class;

    /**
     * @test
     */
    public function shouldNotSupportAnythingNotStatusRequest()
    {
        $action = new StatusAction();
        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
     * @test
     */
    public function shouldMarkNewIfStatusIsNotSet()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([]);

        $action->execute($request);

        $this->assertTrue($request->isNew());
    }

    /**
     * @test
     */
    public function shouldMarkNewIfStatusIsProcessing()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([
            'status' => PaymentStatus::STATE_PROCESSING->value,
        ]);

        $action->execute($request);

        $this->assertTrue($request->isNew());
    }

    /**
     * @test
     */
    public function shouldMarkCapturedIfStatusIsPaid()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([
            'status' => PaymentStatus::STATE_PAID->value,
        ]);

        $action->execute($request);

        $this->assertTrue($request->isCaptured());
    }

    /**
     * @test
     */
    public function shouldMarkFailedIfStatusIsElse()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([
            'status' => PaymentStatus::STATE_CANCEL->value,
        ]);

        $action->execute($request);

        $this->assertTrue($request->isFailed());
    }
}
