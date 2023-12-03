<?php

namespace Tsetsee\PayumQPay\Action\Api;

use ArrayAccess;
use LogicException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Tsetsee\PayumQPay\Action\Api\BaseApiAwareAction;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CheckPayment;

final class CheckPaymentAction extends BaseApiAwareAction
{
    /**
    * @inheritdoc
    */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var CheckPayment $request */
        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($details['invoice'])) {
            throw new LogicException('bad invoice array');
        }

        /** @var array<string, mixed> $invoice */
        $invoice = $details['invoice'];

        /** @var ?string $invoiceId */
        $invoiceId = $invoice['invoice_id'] ?? null;

        if ($invoiceId === null) {
            throw new LogicException('invoice_id field not found in invoice array');
        }

        $qpayInvoice = $this->api->getInvoice($invoiceId);

        /** @psalm-suppress MixedAssignment */
        $details['invoice_details'] = $qpayInvoice->toArray();

        if ($qpayInvoice->invoiceStatus === 'CLOSED') {
            $details['status'] = PaymentStatus::STATE_PAID->value;
        }

        $request->setModel($details);
    }

    /**
     * @inheritDoc
     */
    public function supports($request)
    {
        return
            $request instanceof CheckPayment &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
