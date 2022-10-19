<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    #[Route('/create/user', name: 'app_create_user')]
    public function index(): Response
    {
        return $this->render('create_user/index.html.twig', [
            'controller_name' => 'CreateUserController',
        ]);
    }
}
