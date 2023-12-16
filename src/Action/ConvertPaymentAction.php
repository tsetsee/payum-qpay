<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Tsetsee\PayumQPay\Enum\PaymentStatus;

class ConvertPaymentAction implements ActionInterface
{
    /**
     * @param Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $model = ArrayObject::ensureArrayObject($payment->getDetails());
        $model['number'] = $payment->getNumber();
        $model['clientId'] = $payment->getClientId();
        $model['amount'] = $payment->getTotalAmount();
        $model['currency'] = $payment->getCurrencyCode();
        $model['email'] = $payment->getClientEmail();
        $model['status'] = PaymentStatus::STATE_NEW->value;

        $request->setResult((array) $model);
    }

    public function supports($request)
    {
        return
            $request instanceof Convert
            && $request->getSource() instanceof PaymentInterface
            && 'array' == $request->getTo()
        ;
    }
}
