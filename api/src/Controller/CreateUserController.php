<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;





class CreateUserController extends AbstractController
{
    #[Route('/register', methods:["POST"],name: 'app_create_user')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): JsonResponse
    {
        /*
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $returnMessage = json_encode(["message"=>"Hola"]);
        $response->setContent($returnMessage);

        return $response;
        */
        // username will be email

        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $email = $requestBodyAsJSON['email'];
        $username = $requestBodyAsJSON['username'];

        $user = $userRepository->findOneBy(['email' => $email]);

        //dd($user);
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        if (!is_null($user)) {
            $returnMessage = json_encode(["message"=>"User already exists"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(403);
            return $response;
        }
        // ... e.g. get the user data from a registration form
        $user = new User();
        $plaintextPassword = $requestBodyAsJSON['password'];

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setUsername($username);
        $em = $doctrine->getManager();
        $em->persist($user);
        $em->flush($user);

        $returnMessage = json_encode(["message"=>"User created succesfully", "token"=>"THIS IS A DUMMY"]);

        $response->setContent($returnMessage);
        $response->setStatusCode(201);

        return $response;
        //return $user;
    }
}
