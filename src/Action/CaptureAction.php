<?php

namespace Tsetsee\PayumQPay\Action;

use ArrayAccess;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\Request\Sync;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CreateInvoice;

class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $token = $request->getToken();
        if ($token === null) {
            return;
        }

        $notifyToken = $this->tokenFactory->createNotifyToken(
            $token->getGatewayName(),
            $token->getDetails(),
        );

        if(empty($details['status'])) {
            return;
        }

        if ($details['status'] === PaymentStatus::STATE_NEW->value) {
            $details['notification_url'] = $notifyToken->getTargetUrl();
            $request->setModel($details);

            $this->gateway->execute(new CreateInvoice($request->getModel()));
        } else {
            $this->gateway->execute(new Sync($request->getModel()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
