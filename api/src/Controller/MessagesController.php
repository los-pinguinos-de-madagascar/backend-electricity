<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MessagesController extends AbstractController
{
    #[Route('/users/{id}/messages', name: 'add_message', methods: ["POST"])]
    public function addMessage(EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userSender = $this->getUser();
        $response = new JsonResponse();

        if ($userSender === null){
            $returnMessage = json_encode(["message"=>"The user logged is incorrect"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $requestBodyAsJSON = json_decode($request->getContent(), true);

        $data = $requestBodyAsJSON['data'];
        $format = 'Y-m-d H:i:s';
        $dataMsg = DateTimeImmutable::createFromFormat($format, $data);

        $text = $requestBodyAsJSON['text'];
        $idReceiver = $requestBodyAsJSON['idReceiver'];
        $userReceiver = $userRepository->find($idReceiver);

        if ($userReceiver === null){
            $returnMessage = json_encode(["message"=>"The user introduced does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }

        $message = new Message();
        $message->setData($dataMsg);
        $message->setText($text);
        $message->setSender($userSender);
        $message->setReceiver($userReceiver);

        //persist changes to db
        $entityManager->persist($message);
        //EXECUTE THE ACTUAL QUERIES
        $entityManager->flush();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($userSender,'json'));
        $response->setStatusCode(201);
        return $response;

    }

    #[Route('/users/{id}/messages', name: 'get_messages', methods: ["GET"])]
    public function getConversation(Request $request, MessageRepository $messageRepository, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $user1 = $this->getUser();
        $user1Id = $user1->getId();

        $requestBodyAsJSON = json_decode($request->getContent(), true);
        $user2Id = $requestBodyAsJSON['reciever'];
        $user2 = $userRepository->find($user2Id);

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        if ($user2 === null || $user1 === null) {
            $returnMessage = json_encode(["message"=>"User does not exist"]);
            $response->setContent($returnMessage);
            $response->setStatusCode(404);
            return $response;
        }


        $rebuts = $messageRepository->findBy(array('receiver' => $user1Id,'sender' => $user2Id));
        $enviats = $messageRepository->findBy(array('receiver' => $user2Id,'sender' => $user1Id));


        $response->setStatusCode(201);
        $responseArray['message'] = "Chat from $user1Id to $user2Id";
        $responseArray['enviats'] = $enviats;
        $responseArray['rebuts'] = $rebuts;
        $response->setContent($serializer->serialize($responseArray,'json'));
        return $response;


    }
}
