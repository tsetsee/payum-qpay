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
    public function should_not_support_anything_not_status_request()
    {
        $action = new StatusAction();
        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
    * @test
    */
    public function should_mark_new_if_status_is_not_set()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([]);

        $action->execute($request);

        $this->assertTrue($request->isNew());
    }

    /**
    * @test
    */
    public function should_mark_new_if_status_is_processing()
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
    public function should_mark_captured_if_status_is_paid()
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
    public function should_mark_failed_if_status_is_else()
    {
        $action = new StatusAction();
        $request = new GetHumanStatus([
            'status' => PaymentStatus::STATE_CANCEL->value,
        ]);

        $action->execute($request);

        $this->assertTrue($request->isFailed());
    }
}
