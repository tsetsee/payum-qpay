<?php

namespace Tsetsee\PayumQPay\Action\Api;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Convert;
use Tsetsee\PayumQPay\Enum\PaymentStatus;
use Tsetsee\PayumQPay\Request\CreateInvoice;

final class CreateInvoiceAction extends BaseApiAwareAction
{
    /**
     * @param CreateInvoice $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        /** @var ?int $status */
        $status = $details['status'];

        if ($status !== PaymentStatus::STATE_NEW->value) {
            throw new \LogicException('invalid status code: '.(string) $status);
        }

        try {
            if (!isset($details['amount'])) {
                $details['status'] = PaymentStatus::STATE_CANCEL->value;
                $request->setModel((array) $details);

                return;
            }

            $convert = new Convert($details, 'qpay');
            $this->gateway->execute($convert);

            /** @phpstan-ignore-next-line */
            $invoice = $this->api->createInvoice($convert->getResult());

            $details['status'] = PaymentStatus::STATE_PROCESSING->value;
            $details['invoice'] = (array) $invoice->toArray();
        } catch (BadResponseException $e) {
            $details['status'] = PaymentStatus::STATE_CANCEL->value;
            $details['error'] = $e->getMessage();
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            $details['status'] = $response?->getStatusCode();
            $details['error'] = (string) $response?->getBody();
        }
        $request->setModel((array) $details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof CreateInvoice
            && $request->getModel() instanceof \ArrayAccess
        ;
    }
}
