<?php

namespace Tsetsee\PayumQPay\Action\Api;

use ArrayAccess;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use LogicException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CreateInvoice;

final class CreateInvoiceAction extends BaseApiAwareAction
{
    /**
    * @inheritdoc
    * @param CreateInvoice $request
    */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        /** @var ?int $status */
        $status = $details['status'];

        if ($status !== PaymentStatus::STATE_NEW->value) {
            throw new LogicException('invalid status code: ' . (string) $status);
        }

        try {
            if (empty($details['amount'])) {
                $details['status'] = PaymentStatus::STATE_CANCEL->value;

                return;
            }

            $convert = new Convert($details, 'qpay');
            $this->gateway->execute($convert);

            $invoice = $this->api->createInvoice($convert->getResult());

            $details['status'] = PaymentStatus::STATE_PROCESSING->value;
            $details['invoice'] = (array) $invoice->toArray();
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            $details['status'] = $response?->getStatusCode();
        } catch(BadResponseException $e) {
            $details['status'] = PaymentStatus::STATE_CANCEL->value;
            $details['error'] = $e->getMessage();
        }

        $request->setModel((array) $details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof CreateInvoice &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
