<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/** Applies the session-stored UI language to each request. */
final class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->getSession()->get('_locale');
        if (\is_string($locale) && '' !== $locale) {
            $request->setLocale($locale);
        }
    }

    public static function getSubscribedEvents(): array
    {
        // Before the router's locale listener (priority 16).
        return [KernelEvents::REQUEST => [['onKernelRequest', 20]]];
    }
}
