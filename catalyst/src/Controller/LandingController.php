<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Placeholder landing page: the home the intro reveals and the brand links to. */
final class LandingController extends AbstractController
{
    #[Route('/', name: 'app_landing', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('landing/index.html.twig');
    }
}
