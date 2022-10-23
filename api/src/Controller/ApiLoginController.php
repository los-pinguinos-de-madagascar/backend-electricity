<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/login', name: 'api_login')]
    /*
     * TODO
     * Use normalization and denormalization groups to not return password
     * * */
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        //WARNING THIS METHOD IS CURRENTLY NOT SECURE
        $user = new User();
        $entityManager = $doctrine->getManager();


        //access token should be generated here
        $access_token = "12345";

        $user = $this->getUser();
        //calling constructor for apitoken with named paremeters
        $apiToken = new ApiToken(token: $access_token, tokenOwner: $user);
        //persist
        $entityManager->persist($apiToken);

        $user->addApiToken($apiToken);
        //persist changes to db
        $entityManager->persist($user);
        //EXECUTE THE ACTUAL QUERIES
        $entityManager->flush();

        $user->removeApiToken($apiToken);
        return $this->json([
            'message' => 'Successfull login',
            'token' => $apiToken->getToken(),
            'user' => $user
        ]);
    }
}
