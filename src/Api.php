<?php

namespace Tsetsee\PayumQPay;

use Tsetsee\Qpay\Api\DTO\CheckPaymentRequest;
use Tsetsee\Qpay\Api\DTO\CheckPaymentResponse;
use Tsetsee\Qpay\Api\DTO\CreateInvoiceRequest;
use Tsetsee\Qpay\Api\DTO\CreateInvoiceResponse;
use Tsetsee\Qpay\Api\DTO\GetInvoiceResponse;
use Tsetsee\Qpay\Api\DTO\Offset;
use Tsetsee\Qpay\Api\Enum\Env;
use Tsetsee\Qpay\Api\Enum\ObjectType;
use Tsetsee\Qpay\Api\QPayApi;

class Api
{
    private QPayApi $client;

    public function __construct(
        private string $username,
        private string $password,
        private Env $env,
        private string $invoiceCode,
    ) {
    }

    /**
     * @param array<string, mixed> $options - [
     *                                      ?Psr\Log\LoggerInterface $logger
     *                                      ]
     */
    public function setup(array $options): void
    {
        $this->client = new QPayApi(
            username: $this->username,
            password: $this->password,
            env: $this->env,
            options: $options,
        );
    }

    /**
     * @param array<string, mixed> $details
     */
    public function createInvoice(array $details): CreateInvoiceResponse
    {
        return $this->client->createInvoice(
            CreateInvoiceRequest::from(
                array_merge(['invoiceCode' => $this->invoiceCode], $details),
            ),
        );
    }

    public function getInvoice(string $invoiceId): GetInvoiceResponse
    {
        return $this->client->getInvoice($invoiceId);
    }

    public function checkPayment(
        ObjectType $objectType,
        string $objectId,
        Offset $offset = null,
    ): CheckPaymentResponse {
        return $this->client->checkPayment(CheckPaymentRequest::from([
            'objectType' => $objectType->value,
            'objectId' => $objectId,
            'offset' => $offset ?? Offset::from([
                'pageNumber' => 1,
                'pageLimit' => 100,
            ]),
        ]));
    }
}
