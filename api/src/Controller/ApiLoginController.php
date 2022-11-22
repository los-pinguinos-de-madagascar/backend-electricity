<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiLoginController extends AbstractController
{
    #[Route('/login', name: 'api_login')]
    public function index(ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        //WARNING THIS METHOD IS CURRENTLY NOT SECURE
        $user = new User();
        $entityManager = $doctrine->getManager();

        $user = $this->getUser();

        $access_token = bin2hex(random_bytes(125));
        $apiToken = new ApiToken(token: $access_token, tokenOwner: $user);

        $user->addApiToken($apiToken);
        //persist changes to db
        $entityManager->persist($user);
        $entityManager->persist($apiToken);
        //EXECUTE THE ACTUAL QUERIES
        $entityManager->flush();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        $tokens = $user->getApiTokens();

        $serializedTokens = null;
        $tokens = $user->getApiTokens();

        foreach($tokens as $token){
            $serializedTokens[] = $token->getToken();
        }
        $jsonizedTokens = $serializer->serialize($serializedTokens, 'json');
        $user->userTokens = $jsonizedTokens;
        $serializedUser = $serializer->serialize($user,'json');
        $serializedUser = json_decode($serializedUser);
        $serializedUser->apiTokensValues = $serializedTokens;
        $serializedUser = json_encode($serializedUser);
        $response->setContent($serializedUser);
        $response->setStatusCode(201);

        return $response;
    }
}
