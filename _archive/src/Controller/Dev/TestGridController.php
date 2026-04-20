<?php

namespace App\Controller\Dev;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dev', name: 'dev_', env: 'dev')]
class TestGridController extends AbstractController
{
    #[Route('/grid-test', name: 'grid_test', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('dev/grid_test.html.twig', [
            'columns' => 9,
            'rows' => 9,
        ]);
    }
}
