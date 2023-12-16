<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;

class ConvertQPayAction implements ActionInterface
{
    /**
     * @param Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getSource());
        $model = ArrayObject::ensureArrayObject($details['qpay'] ?? []);

        /** @var string $number */
        $number = $details['number'];

        $model->defaults([
            'senderInvoiceNo' => $number,
            'invoiceReceiverCode' => $details['clientId'],
            'invoiceDescription' => sprintf('invoice no: %s', $number),
            'senderBranchCode' => 'CENTRAL',
            'amount' => round(
                $details['amount'] / 100.0,
                precision: 2,
                mode: PHP_ROUND_HALF_DOWN
            ),
            'callbackUrl' => $details['notification_url'],
        ]);

        $request->setResult((array) $model);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert
            && $request->getSource() instanceof \ArrayAccess
            && 'qpay' == $request->getTo()
        ;
    }
}
