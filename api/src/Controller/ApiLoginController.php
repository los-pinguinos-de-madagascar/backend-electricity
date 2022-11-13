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

        $user = $this->getUser();
        $bad_token = true;
        //while ($bad_token) {
            //try {
                //calling constructor for apitoken with named paremeters
                $access_token = bin2hex(random_bytes(254));
                $apiToken = new ApiToken(token: $access_token, tokenOwner: $user);

                $user->addApiToken($apiToken);
                //persist changes to db
                $entityManager->persist($user);
                $entityManager->persist($apiToken);
                //EXECUTE THE ACTUAL QUERIES
                $entityManager->flush();
            //} catch (\Exception $e) {
            //    $user->removeApiToken($apiToken);
            //}
            $bad_token = false;
        //}
        $serialized_tokens = null;
        $tokens = $user->getApiTokens();
        foreach($tokens as $token){
            $serialized_tokens[] = $token->getToken();
        }

        return $this->json([
            'message' => 'Successfull login',
            'user' => ['email' => $user->getEmail(), 'tokens' => $serialized_tokens]
        ]);
    }
}
