<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\AwardRepository;
use App\Repository\BicingStationRepository;
use App\Repository\LocationRepository;
use App\Repository\RechargeStationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/users/{id}/locations', name: 'add_favourite_location', methods: ["POST"])]
    public function index(Request $request, ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();

        $location = $serializer->deserialize($request->getContent(), Location::class, 'json');

        $user->addFavouriteLocation($location);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/users/{id}/locations/{idLocation}', name: 'delete_favourite_location', methods: ["DELETE"])]
    public function delete(ManagerRegistry $doctrine, SerializerInterface $serializer, LocationRepository $locationRepository, int $idLocation): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $response = new JsonResponse();

        $location = $locationRepository->find($idLocation);

        if ($location === null){
            $returnMessage = json_encode(["message"=>"Location does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $user->removeFavouriteLocation($location);
        $entityManager->remove($location);
        $entityManager->flush();

        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/users/{id}/bicingStation/{idBicingStation}', name: 'add_bicing_station', methods: ["POST"])]
    public function addFavouriteBicingStation(ManagerRegistry $doctrine, SerializerInterface $serializer, BicingStationRepository $bicingStationRepository, int $idBicingStation): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();

        $bicingStation = $bicingStationRepository->find($idBicingStation);

        $response = new JsonResponse();

        if ($bicingStation === null){
            $returnMessage = json_encode(["message"=>"Bicing station $idBicingStation does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $user->addFavouriteBicingStation($bicingStation);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/users/{id}/rechargeStation/{idRechargeStation}', name: 'add_recharge_station', methods: ["POST"])]
    public function addFavouriteRechargeStation(ManagerRegistry $doctrine, SerializerInterface $serializer, RechargeStationRepository $rechargeStationRepository , int $idRechargeStation): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $response = new JsonResponse();

        $rechargeStation = $rechargeStationRepository->find($idRechargeStation);

        if ($rechargeStation === null){
                $returnMessage = json_encode(["message"=>"Recharge station $idRechargeStation does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $user->addFavouriteRechargeStation($rechargeStation);

        $entityManager->persist($user);
        $entityManager->flush();

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/users/{idUser}/bicingStation/{idBicingStation}', name: 'delete_favourite_bicingStation', methods: ["DELETE"])]
    public function deleteFavouriteBicingStation(ManagerRegistry $doctrine, SerializerInterface $serializer, BicingStationRepository $bicingStationRepository, int $idBicingStation): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $response = new JsonResponse();

        $bicingStation = $bicingStationRepository->find($idBicingStation);

        if ($bicingStation === null){
            $returnMessage = json_encode(["message"=>"Bicing station $idBicingStation does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $user->removeFavouriteBicingStation($bicingStation);
        $entityManager->flush();

        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/users/{id}/rechargeStation/{idRechargeStation}', name: 'delete_favourite_rechargeStation', methods: ["DELETE"])]
    public function deleteFavouriteRechargeStation(ManagerRegistry $doctrine, SerializerInterface $serializer, RechargeStationRepository $rechargeStationRepository, int $idRechargeStation): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $response = new JsonResponse();

        $rechargeStation = $rechargeStationRepository->find($idRechargeStation);

        if ($rechargeStation === null){
            $returnMessage = json_encode(["message"=>"Recharge station $idRechargeStation does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $user->removeFavouriteRechargeStation($rechargeStation);
        $entityManager->flush();

        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);
        return $response;
    }
    #[Route('/profile', name: 'api_login', methods: ['GET'])]
    public function showProfile(ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(201);

        return $response;
    }

    #[Route('/users/{id}/password', name: 'change_password', methods: ['PUT'])]
    public function changePassword(Request $request): JsonResponse{

        $token = $request->query->get("token");

        dd($token);
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        return $response;

    }

    #[Route('/{email}/user', name: 'getUserByEmail', methods: ['GET'])]
    public function getUserByEmail(Request $request, UserRepository $userRepository, SerializerInterface $serializer, String $email): JsonResponse{

        $requestBodyAsJSON = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy(['email' => $email]);
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        if (is_null($user)) {
            $returnMessage = json_encode(["message"=>"User does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);

            return $response;
        }


        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(200);

        return $response;

    }

    #[Route('/users/{id}/awards/{idAward}', name: 'add_user_award', methods: ["POST"])]
    public function addUserAward(ManagerRegistry $doctrine, SerializerInterface $serializer, AwardRepository $awardRepository , int $idAward): JsonResponse
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $response = new JsonResponse();

        $award = $awardRepository->find($idAward);

        if ($award === null){
            $returnMessage = json_encode(["message"=>"Award $idAward does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $userCoins = $user->getElectryCoins();
        $awardPrice = $award->getPrice();

        if (($userCoins !== null) && ($awardPrice <= $userCoins)){
            $user->setElectryCoins($userCoins - $awardPrice);
            $user->addAward($award);

            $entityManager->persist($user);
            $entityManager->flush();

            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($serializer->serialize($user,'json'));
            $response->setStatusCode(201);
        }

        else{
            $returnMessage = json_encode(["message"=>"The user does not have enough electryCoins to buy the award $idAward"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(200);
        }
        return $response;
    }

}
