<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\BicingStationRepository;
use App\Repository\CommentRepository;
use App\Repository\RechargeStationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    #[Route('/bicing_stations/{idStation}/comments', name: 'add_bicing_comment', methods: ["POST"])]
    public function commentBicingStation(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, BicingStationRepository $bicingStationRepository, int $idStation): JsonResponse
    {
        $user = $this->getUser();

        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $msg = $requestBodyAsJSON['message'];
        $data = $requestBodyAsJSON['date'];

        $format = 'Y-m-d H:i:s';
        $date = DateTimeImmutable::createFromFormat($format, $data);

        $station = $bicingStationRepository->find($idStation);

        $response = new JsonResponse();
        if ($station === null){
            $returnMessage = json_encode(["message"=>"Station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $comment = new Comment();
        $comment->setDate($date);
        $comment->setMessage($msg);
        $comment->setUserOwner($user);
        $comment->setBicingStation($station);
        $entityManager->persist($comment);
        $entityManager->flush();

        $response->setContent($serializer->serialize($comment,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/recharge_stations/{idStation}/comments', name: 'add_recharge_comment', methods: ["POST"])]
    public function commentRechargeStation(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, RechargeStationRepository $rechargeStationRepository, int $idStation): JsonResponse
    {
        $user = $this->getUser();

        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $msg = $requestBodyAsJSON['message'];
        $data = $requestBodyAsJSON['date'];

        $format = 'Y-m-d H:i:s';
        $date = DateTimeImmutable::createFromFormat($format, $data);

        $station = $rechargeStationRepository->find($idStation);

        $response = new JsonResponse();
        if ($station === null){
            $returnMessage = json_encode(["message"=>"Station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $comment = new Comment();
        $comment->setDate($date);
        $comment->setMessage($msg);
        $comment->setUserOwner($user);
        $comment->setRechargeStation($station);
        $entityManager->persist($comment);
        $entityManager->flush();

        $response->setContent($serializer->serialize($comment,'json'));
        $response->setStatusCode(201);
        return $response;
    }

    #[Route('/recharge_stations/{idStation}/comments', name: 'get_comments_recharge_station', methods: ["GET"])]
    public function getCommentsRecharge(CommentRepository $commentRepository, SerializerInterface $serializer, RechargeStationRepository $rechargeStationRepository,int $idStation): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        $station = $rechargeStationRepository->find($idStation);

        if ($station === null){
            $returnMessage = json_encode(["message"=>"Station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $comments = $commentRepository->findBy(['rechargeStation' => $station]);

        $response->setStatusCode(200);
        $responseArray['message'] = "Comments of $idStation";
        $responseArray['comments'] = $comments;
        $response->setContent($serializer->serialize($responseArray,'json'));
        return $response;
    }

    #[Route('/bicing_stations/{idStation}/comments', name: 'get_comments_bicing_station', methods: ["GET"])]
    public function getCommentsBicing(CommentRepository $commentRepository, SerializerInterface $serializer, BicingStationRepository $bicingStationRepository, int $idStation): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        $station = $bicingStationRepository->find($idStation);

        if ($station === null){
            $returnMessage = json_encode(["message"=>"Station does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $comments = $commentRepository->findBy(['bicingStation' => $station]);

        $response->setStatusCode(200);
        $responseArray['message'] = "Comments of $idStation";
        $responseArray['comments'] = $comments;
        $response->setContent($serializer->serialize($responseArray,'json'));
        return $response;
    }

    #[Route('/comments/{idComment}', name: 'modify_messages', methods: ["PUT"])]
    public function modifyCommentsBicing(Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository, SerializerInterface $serializer, int $idComment): JsonResponse
    {
        $requestBodyAsJSON = json_decode($request->getContent(), true);
        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        $msg = $requestBodyAsJSON['message'];

        $comment = $commentRepository->find($idComment);
        $user = $this->getUser();

        if ($comment === null) {
            $returnMessage = json_encode(["message"=>"Comment does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        if ($user->getId() !== $comment->getUserOwner()->getId()) {
            $returnMessage = json_encode(["Not Authorized"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(401);
            return $response;
        }

        $comment->setMessage($msg);
        $entityManager->persist($comment);
        $entityManager->flush();

        $response->setStatusCode(200);
        $responseArray['message'] = "Comment $idComment modified";
        $responseArray['comment'] = $comment;
        $response->setContent($serializer->serialize($responseArray,'json'));
        return $response;
    }

    #[Route('/users/{idUser}/comments/{idComment}', name: 'delete_comment', methods: ["DELETE"])]
    public function deleteFavouriteBicingStation(EntityManagerInterface $entityManager, SerializerInterface $serializer, CommentRepository $commentRepository, int $idComment): JsonResponse
    {
        $user = $this->getUser();
        $response = new JsonResponse();

        $comment = $commentRepository->find($idComment);

        if ($comment === null){
            $returnMessage = json_encode(["message"=>"Comment does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        if ($comment->getUserOwner()->getId() !== $user->getId()){
            $returnMessage = json_encode(["Not Authorized, the user is not the owner of the comment"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(401);
            return $response;
        }

        $user->removeComment($comment);
        $entityManager->flush();

        $response->setContent($serializer->serialize($user,'json'));
        $response->setStatusCode(200);
        return $response;
    }
}
