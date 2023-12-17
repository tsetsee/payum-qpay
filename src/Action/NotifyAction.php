<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Sync;
use Tsetsee\PayumQPay\Action\Api\BaseApiAwareAction;

class NotifyAction extends BaseApiAwareAction
{
    /**
     * @param Notify $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute(new Sync($details));

        throw new HttpResponse('SUCCESS', 200);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Notify
            && $request->getModel() instanceof \ArrayAccess
        ;
    }
}
