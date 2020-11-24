<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Deal;
use App\Entity\Group;
use App\Entity\PromoCode;
use App\Entity\Rate;
use App\Event\DealCommentEvent;
use App\Event\DealPostEvent;
use App\Form\CommentType;
use App\Form\PromoCodeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromoCodeController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher =$eventDispatcher;
    }
    /**
     * @Route("/promocodes", name="promocodes")
     */
    public function index()
    {
        $promocodes = $this->getDoctrine()->getRepository(PromoCode::class)->findAll();
        $hotcodes = $this->getDoctrine()->getRepository(PromoCode::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('promo_code/index.html.twig', [
            'promocodes' => $promocodes,
            'hotcodes' => $hotcodes,
            'hot' => "",
            'top' => "active",
            'type' => PromoCode::TYPE_NAME
        ]);
    }

    /**
     * @Route("/promocodes/hot", name="codes_hot")
     */
    public function indexHot()
    {
        $em = $this->getDoctrine()->getManager();
        $promocodes = $em->getRepository(PromoCode::class)->findAllOrderByHot();
        $hotcodes = $em->getRepository(PromoCode::class)->findAllOrderByXHot(HomeController::NB_HOT);
        return $this->render('promo_code/index.html.twig', [
            'promocodes' => $promocodes,
            'hotcodes' => $hotcodes,
            'hot' => "active",
            'top' => "",
            'type' => PromoCode::TYPE_NAME
        ]);
    }
    
    /**
     * @Route("/promocode/create", name = "create_promoCode")
     */
    public function createPromoCode(Request $request)
    {
        $promoCode = new PromoCode();
        $form = $this->createForm(PromoCodeType::class, $promoCode);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $promoCode->setRateValue(0);
            if ($this->getUser() != null) {
                $promoCode->setAuthor($this->getUser());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($promoCode);
            $event = new DealPostEvent($this->getUser(), count($this->getDoctrine()->getRepository(Deal::class)->findAllByAuthorId($this->getUser()->getId())));
            $this->eventDispatcher->dispatch($event);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('promo_code/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/promocode{promocodeid}",defaults={"promocodeid" = null}, name="promocode")
     */
    public function displayPromoCode($promocodeid, Request $request)
    {
        $promoCode = $this->getDoctrine()->getRepository(PromoCode::class)->findOneBy(["id" => $promocodeid]);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(["deal" => $promocodeid], ["date" => 'DESC']);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $comment->setAuthor($this->getUser());
            $comment->setDate(new \DateTime('now'));
            $promoCode->addComments($comment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($promoCode);
            $event = new DealCommentEvent($this->getUser(), count($this->getDoctrine()->getRepository(Comment::class)->findAllByAuthorId($this->getUser()->getId())));
            $this->eventDispatcher->dispatch($event);
            $em->flush();
            $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(["deal" => $promocodeid], ["date" => 'DESC']);
        }

        return $this->render('promo_code/promocode.html.twig', [
            'form' => $form->createView(),
            'comments' => $comments,
            'promocode' => $promoCode
        ]);
    }
}
