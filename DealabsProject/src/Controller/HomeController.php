<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\Deal;
use App\Entity\PromoCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const NB_HOT = 4;
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Deal::class)->findAllWeeklyDealsOrderByCommentCount();
        $hotdeals = $em->getRepository(Deal::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('deal/index.html.twig', [
            'deals' => $entities,
            'hotdeals' => $hotdeals,
            'hot' => "",
            'top' => "active",
            'news' => "",
            'topcommented' => "",
            'type' => Deal::TYPE_NAME
        ]);
    }

    /**
     * @Route("/hot", name="hot")
     */
    public function indexHot()
    {
        $em = $this->getDoctrine()->getManager();
        $plans = $em->getRepository(Deal::class)->findAllOrderByHot();
        $hotplans = $em->getRepository(Deal::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('deal/index.html.twig', [
            'deals' => $plans,
            'hotdeals' => $hotplans,
            'hot' => "active",
            'top' => "",
            'news' => "",
            'topcommented' => "",
            'type' => Deal::TYPE_NAME
        ]);
    }

    /**
     * @Route("/news",name="news")
     */
    public function indexNews()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Deal::class)->findAllOrderByDate();
        $hotplans = $em->getRepository(Deal::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('deal/index.html.twig', [
            'deals' => $entities,
            'hotdeals' => $hotplans,
            'hot' => "",
            'top' => "",
            'news' => "active",
            'topcommented' => "",
            'type' => Deal::TYPE_NAME
        ]);
    }

    /**
     * @Route("/commented",name="commented")
     */
    public function indexComments()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Deal::class)->findAllOrderByCommentCount();
        $hotplans = $em->getRepository(Deal::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('deal/index.html.twig', [
            'deals' => $entities,
            'hotdeals' => $hotplans,
            'hot' => "",
            'top' => "",
            'news' => "",
            'topcommented' => "active",
            'type' => Deal::TYPE_NAME
        ]);
    }
}
