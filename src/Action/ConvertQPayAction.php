<?php

namespace Tsetsee\PayumQPay\Action;

use ArrayAccess;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Convert;
use Tsetsee\Qpay\Api\DTO\CreateInvoiceRequest;

class ConvertQPayAction implements ActionInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getSource());
        $model = ArrayObject::ensureArrayObject($details['qpay']);

        $model->defaults([
            'senderInvoiceNo' => $details['number'],
            'invoiceReceiverCode' => $details['clientId'],
            'invoiceDescription' => sprintf('invoice no: %s', $details['number']),
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

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof ArrayAccess &&
            $request->getTo() == 'qpay'
        ;
    }
}
