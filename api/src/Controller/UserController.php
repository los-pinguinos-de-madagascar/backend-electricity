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
    public function index(Request $request, ManagerRegistry $doctrine, LocationRepository $locationRepository, SerializerInterface $serializer): JsonResponse
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
}
