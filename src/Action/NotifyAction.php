<?php

namespace Tsetsee\PayumQPay\Action;

use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Payum\Core\Request\Sync;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use Tsetsee\PayumQPay\Action\Api\BaseApiAwareAction;

class NotifyAction extends BaseApiAwareAction
{
    private ?StorageInterface $storage = null;

    /**
     * @param Notify $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /* @var Notify $request */
        /** @var IdentityInterface $model */
        $model = $request->getModel();

        if (null === $this->storage) {
            throw new LogicException('storage is empty');
        }

        $payment = $this->storage->find($model);

        $this->gateway->execute(new Sync($payment));

        throw new HttpResponse('SUCCESS', 200);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Notify
            && $request->getModel() instanceof IdentityInterface
        ;
    }

    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }
}
