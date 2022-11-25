<?php

namespace App\Controller;

use App\Entity\Location;
use App\Repository\LocationRepository;
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

}
