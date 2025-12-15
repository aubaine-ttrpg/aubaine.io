<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin_', env: 'dev')]
class AdminDashboardController extends AdminController
{
    #[Route('', name: 'home', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
