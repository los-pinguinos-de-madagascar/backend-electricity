<?php

namespace App\Controller;

use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    #[Route('/users/register', name: 'app_create_user')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $returnMessage = json_encode(["message"=>"Hola"]);
        $response->setContent($returnMessage);

        return $response;

        // username will be email
        $email = $request->get("username");
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if (!is_null($user)) {
            $returnMessage = json_encode(["message"=>"User already exists"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(401);
            return $response;
        }
        // ... e.g. get the user data from a registration form
        $user = new User();
        $plaintextPassword = $request->get("password");

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setEmail($email);
        $user->setPassword($hashedPassword);


        //$response->setContent()
        //$response->setStatusCode(201);
        return $user;
    }
}
