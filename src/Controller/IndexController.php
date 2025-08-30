<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index/home.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('index/about.html.twig', [
        ]);
    }

    #[Route('/contribute', name: 'app_contribute')]
    public function contribute(): Response
    {
        return $this->render('index/contribute.html.twig', [
        ]);
    }

    #[Route('/changelog', name: 'app_changelog')]
    public function changelog(): Response
    {
        return $this->render('index/changelog.html.twig', [
        ]);
    }
}