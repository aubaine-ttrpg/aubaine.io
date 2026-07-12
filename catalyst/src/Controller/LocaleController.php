<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Stores the chosen UI language in the session and returns where we came from. */
final class LocaleController extends AbstractController
{
    #[Route('/locale/{locale}', name: 'app_set_locale', requirements: ['locale' => 'en|fr'], methods: ['GET'])]
    public function set(string $locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?? $this->generateUrl('app_book_index'));
    }
}
