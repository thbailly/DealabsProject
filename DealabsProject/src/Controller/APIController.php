<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api", name="api_")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/weeklyDeals", name="weeklyDeals", methods={"GET"})
     */
    public function getWeeklyDeals()
    {
        $deals = $this->getDoctrine()->getRepository(Deal::class)->findWeeklyDealsForAPI();
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonContent = $serializer->serialize($deals, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/weeklyDeals/{dealid}",defaults={"dealid" = null}, name="weeklyDealsById", methods={"GET"})
     */
    public function getWeeklyDealsById($dealid)
    {
        if ($dealid == null) {
            return new Response('Invalid ID', 404);
        }
        $deal = $this->getDoctrine()->getRepository(Deal::class)->findWeeklyDealsForAPIById($dealid);
        if ($deal == null) {
            return new Response('Invalid ID', 404);
        }
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonContent = $serializer->serialize($deal, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/users/{userId}/savedDeals",defaults={"userId" = null}, name="users_savedDeals", methods={"GET"})
     */
    public function getSavedDealsForCurrentUser($userId)
    {
        if ($userId == null) {
            return new Response("no user id specified", 404);
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        if ($user == null) {
            return new Response('Invalid user ID', 401);
        }
        $dealsId = array();
        foreach ($user->getSavedDeals() as $deal) {
            array_push($dealsId, $deal->getId());
        }
        $dealsSaved =  $this->getDoctrine()->getRepository(Deal::class)->findSavedDealsByUserForAPI($dealsId);
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonContent = $serializer->serialize($dealsSaved, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
