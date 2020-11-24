<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use App\Entity\Group;
use App\Entity\Plan;
use App\Entity\Rate;
use App\Entity\User;
use App\Event\DealCommentEvent;
use App\Event\DealPostEvent;
use App\Form\CommentType;
use App\Form\PlanType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlanController extends AbstractController
{
    const NB_HOT = 4;

    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher =$eventDispatcher;
    }
    /**
     * @Route("/plans", name="plans")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $plans = $em->getRepository(Plan::class)->findAllWeeklyDealsOrderByCommentCount();
        $hotplans = $em->getRepository(Plan::class)->findAllOrderByXHot(PlanController::NB_HOT);
        return $this->render('plan/index.html.twig', [
            'plans' => $plans,
            'hotplans' => $hotplans,
            'hot' => "",
            'top' => "active",
            'news' => "",
            'topcommented' => "",
            'type' => Plan::TYPE_NAME
        ]);
    }

    /**
     * @Route("/plans/hot", name="plans_hot")
     */
    public function indexHot()
    {
        $em = $this->getDoctrine()->getManager();
        $plans = $em->getRepository(Plan::class)->findAllOrderByHot();
        $hotplans = $em->getRepository(Plan::class)->findAllOrderByXHot(PlanController::NB_HOT);
        return $this->render('plan/index.html.twig', [
            'plans' => $plans,
            'hotplans' => $hotplans,
            'hot' => "active",
            'top' => "",
            'news' => "",
            'topcommented' => "",
            'type' => Plan::TYPE_NAME
        ]);
    }

    /**
     * @Route("/plans/news",name="plans_news")
     */
    public function indexNews()
    {
        $em = $this->getDoctrine()->getManager();
        $plans = $em->getRepository(Plan::class)->findAllOrderByDate();
        $hotplans = $em->getRepository(Plan::class)->findAllOrderByXHot(PlanController::NB_HOT);
        return $this->render('plan/index.html.twig', [
            'plans' => $plans,
            'hotplans' => $hotplans,
            'hot' => "",
            'top' => "",
            'news' => "active",
            'topcommented' => "",
            'type' => Plan::TYPE_NAME
        ]);
    }

    /**
     * @Route("/plans/commented",name="plans_commented")
     */
    public function indexComments()
    {
        $em = $this->getDoctrine()->getManager();
        $plans = $em->getRepository(Plan::class)->findAllOrderByCommentCount();
        $hotplans = $em->getRepository(Plan::class)->findAllOrderByXHot(PlanController::NB_HOT);
        return $this->render('plan/index.html.twig', [
            'plans' => $plans,
            'hotplans' => $hotplans,
            'hot' => "",
            'top' => "",
            'news' => "",
            'topcommented' => "active",
            'type' => Plan::TYPE_NAME
        ]);
    }

    /**
     * @Route("/plan/create", name="create_plan")
     */
    public function createPlan(Request $request)
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $plan->setRateValue(0);
            if ($this->getUser() != null) {
                $plan->setAuthor($this->getUser());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($plan);
            $event = new DealPostEvent($this->getUser(), count($this->getDoctrine()->getRepository(Deal::class)->findAllByAuthorId($this->getUser()->getId())));
            $this->eventDispatcher->dispatch($event);
            $em->flush();
            return $this->redirectToRoute('home');
        }


        return $this->render('plan/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/plan{planid}",defaults={"planid" = null}, name="plan")
     */
    public function displayPlan($planid, Request $request)
    {
        $plan = $this->getDoctrine()->getRepository(Plan::class)->findOneBy(["id" => $planid]);
        $hotplans = $this->getDoctrine()->getRepository(Plan::class)->findAllOrderByXHot(PlanController::NB_HOT);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(["deal" => $planid], ["date" => 'DESC']);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $comment->setAuthor($this->getUser());
            $comment->setDate(new \DateTime('now'));
            $plan->addComments($comment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($plan);
            $event = new DealCommentEvent($this->getUser(), count($this->getDoctrine()->getRepository(Comment::class)->findAllByAuthorId($this->getUser()->getId())));
            $this->eventDispatcher->dispatch($event);
            $em->flush();
            $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(["deal" => $planid], ["date" => 'DESC']);
        }

        return $this->render('plan/plan.html.twig', [
            'form' => $form->createView(),
            'comments' => $comments,
            'plan' => $plan,
            'hotplans' => $hotplans
        ]);
    }
}
