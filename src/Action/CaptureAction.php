<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Sync;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Tsetsee\PayumQPay\Action\Api\BaseApiAwareAction;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CreateInvoice;

class CaptureAction extends BaseApiAwareAction implements GenericTokenFactoryAwareInterface
{
    use GenericTokenFactoryAwareTrait;

    /**
     * @param Capture $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($details['status'])) {
            return;
        }

        if ($details['status'] === PaymentStatus::STATE_NEW->value) {
            $this->gateway->execute(new CreateInvoice($request->getModel()));
        } else {
            $this->gateway->execute(new Sync($request->getModel()));
        }
    }

    public function supports($request)
    {
        return
            $request instanceof Capture
            && $request->getModel() instanceof \ArrayAccess
        ;
    }
}
