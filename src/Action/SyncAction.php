<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Sync;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CheckPayment;

final class SyncAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Sync $request */
        $details = ArrayObject::ensureArrayObject($request->getModel());

        if ($details['status'] === PaymentStatus::STATE_PROCESSING->value) {
            $this->gateway->execute(new CheckPayment($details));
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof Sync
            && $request->getModel() instanceof \ArrayObject
        ;
    }
}
