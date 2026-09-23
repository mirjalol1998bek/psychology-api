<?php

declare(strict_types=1);

namespace App\Controller\Subscribers;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Controller\Base\AbstractController;
use App\Entity\Interfaces\CreatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedAtSettableInterface;
use App\Entity\Interfaces\UpdatedBySettableInterface;
use App\Entity\Interfaces\CreatedBySettableInterface;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WriteSubscriber extends AbstractController implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onWrite', EventPriorities::PRE_WRITE],
        ];
    }

    public function onWrite(ViewEvent $event): void
    {
        if ($this->isIgnoredUrl($event)) {
            return;
        }

        $model = $event->getControllerResult();

        switch ($event->getRequest()->getMethod()) {
            case Request::METHOD_POST:
                $this->persist($model);
                break;

            case Request::METHOD_PUT:
                $this->update($model);
                break;
        }
    }

    private function persist(object|array $model): void
    {
        if ($this->isExisting($model)) {
            return;
        }

        if ($model instanceof CreatedBySettableInterface) {
            $model->setCreatedBy($this->getUser());
        }

        if ($model instanceof CreatedAtSettableInterface) {
            $model->setCreatedAt(new DateTime());
        }
    }

    private function update(object|array $model): void
    {
        if ($model instanceof UpdatedBySettableInterface) {
            $model->setUpdatedBy($this->getUser());
        }

        if ($model instanceof UpdatedAtSettableInterface) {
            $model->setUpdatedAt(new DateTime());
        }
    }

    /**
     * Har POST yaratish emas — masalan `users/about_me` mavjud User'ni
     * qaytaradi; aks holda uning createdAt/createdBy har safar qayta yozilardi.
     */
    private function isExisting(object|array $model): bool
    {
        return is_object($model) && method_exists($model, 'getId') && $model->getId() !== null;
    }

    private function isIgnoredUrl(ViewEvent $event): bool
    {
        switch ($event->getRequest()->getRequestUri()) {
            case '/api/some_ignored_url':
            case '/api/another_ignored_url':
                return true;

            default:
                return false;
        }
    }
}
