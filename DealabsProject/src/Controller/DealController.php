<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\Group;
use App\Entity\Plan;
use App\Entity\PromoCode;
use App\Entity\Rate;
use App\Entity\User;
use App\Event\DealVoteEvent;
use App\Event\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DealController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher =$eventDispatcher;
    }

    //Factoriser du code des plans et promocodes controller


    /**
     * @Route("/deal{dealid}",defaults={"dealid" = null}, name="deal")
     */
    public function displayDeal($dealid)
    {
        $deal = $this->getDoctrine()->getRepository(Deal::class)->findOneBy(["id" => $dealid]);

        if ($deal instanceof Plan) {
            return $this->redirectToRoute("plan", ['planid' => $dealid]);
        }

        if ($deal instanceof PromoCode) {
            return $this->redirectToRoute("promocode", ['promocodeid' => $dealid]);
        }

        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/deal/{dealid}/addGroup/",defaults={"dealid" = null},  name="deal_addGroupView")
     */
    public function addGroupList($dealid)
    {
        $deal = $this->getDoctrine()->getRepository(Deal::class)->find($dealid);
        $groupsId = $deal->getGroupsId();
        if (sizeof($groupsId) > 0) {
            $groups = $this->getDoctrine()->getRepository(Group::class)->findGroupsNotIn($groupsId);
        } else {
            $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        }
        return $this->render('group/addGroupToDealView.html.twig', [
            'groups' => $groups,
            'dealid' => $dealid
        ]);
    }

    /**
     * @Route("deal/{dealid}/addGroup/{groupid}", defaults={"dealid" = null, "groupid" = null}, name="deal_addGroup")
     */
    public function addGroupAction($dealid, $groupid)
    {
        $group = $this->getDoctrine()->getRepository(Group::class)->find($groupid);
        $deal = $this->getDoctrine()->getRepository(Deal::class)->find($dealid);
        $group->addDeal($deal);
        $this->getDoctrine()->getManager()->persist($group);
        $this->getDoctrine()->getManager()->persist($deal);
        $this->getDoctrine()->getManager()->flush();
        return $this->addGroupList($dealid);
    }

    /**
     * @Route("/deal/up/{id}", defaults={"id" = null}, name="up_deal")
     */
    public function plusDeal($id)
    {
        if ($id == null) {
            return $this->redirectToRoute('home');
        }
        $rate = $this->getDoctrine()->getRepository(Rate::class)->findByUserIdAndDealId($this->getUser()->getId(), $id);
        $deal = $this->getDoctrine()->getRepository(Deal::class)->findOneBy(["id" => $id]);
        if ($rate != null) {
            if (is_array($rate)) {
                if ($rate[0]->getValue() == 1) {
                    if ($deal instanceof PromoCode) {
                        return $this->redirectToRoute('promocode', array("promocodeid" => $id));
                    }

                    if ($deal instanceof Plan) {
                        return $this->redirectToRoute('plan', array("planid" => $id));
                    }
                    
                    return $this->redirectToRoute('home');
                }
            } elseif ($rate->getValue() == 1) {
                if ($deal instanceof PromoCode) {
                    return $this->redirectToRoute('promocode', array("promocodeid" => $id));
                }

                if ($deal instanceof Plan) {
                    return $this->redirectToRoute('plan', array("planid" => $id));
                }
                
                return $this->redirectToRoute('home');
            }
        }

        if ($rate == null) {
            $rate = new Rate();
            $this->getUser()->setNbVote(1);
            if (!empty($this->eventDispatcher)) {
                $event = new DealVoteEvent($this->getUser());
                $this->eventDispatcher->dispatch($event);
            }
        }
        if ($rate != null) {
            if (is_array($rate)) {
                $rate = $rate[0];
            }
        }
        $deal = $this->getDoctrine()->getRepository(Deal::class)->find($id);
        $deal->incrementRateValue();
        $rate->setIdDeal($id);
        $rate->setValue(1);
        $rate->setIdUser($this->getUser()->getId());
        $this->getDoctrine()->getManager()->persist($deal);
        $this->getDoctrine()->getManager()->persist($rate);
        $this->getDoctrine()->getManager()->flush();
        
        if ($deal instanceof PromoCode) {
            return $this->redirectToRoute('promocode', array("promocodeid" => $id));
        }

        if ($deal instanceof Plan) {
            return $this->redirectToRoute('plan', array("planid" => $id));
        }
        
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/deal/down/{id}", defaults={"id" = null}, name="down_deal")
     */
    public function moinsDeal($id)
    {
        if ($id == null) {
            return $this->redirectToRoute('home');
        }
        $rate = $this->getDoctrine()->getRepository(Rate::class)->findByUserIdAndDealId($this->getUser()->getId(), $id);
        $deal = $this->getDoctrine()->getRepository(Deal::class)->findOneBy(["id" => $id]);

        if ($rate != null) {
            if (is_array($rate)) {
                if ($rate[0]->getValue() == -1) {
                    if ($deal instanceof PromoCode) {
                        return $this->redirectToRoute('promocode', array("promocodeid" => $id));
                    }

                    if ($deal instanceof Plan) {
                        return $this->redirectToRoute('plan', array("planid" => $id));
                    }
                    
                    return $this->redirectToRoute('home');
                }
            } elseif ($rate->getValue() == -1) {
                if ($deal instanceof PromoCode) {
                    return $this->redirectToRoute('promocode', array("promocodeid" => $id));
                }

                if ($deal instanceof Plan) {
                    return $this->redirectToRoute('plan', array("planid" => $id));
                }
                
                return $this->redirectToRoute('home');
            }
        }

        if ($rate == null) {
            $rate = new Rate();
            $this->getUser()->setNbVote(1);
            if (!empty($this->eventDispatcher)) {
                $event = new DealVoteEvent($this->getUser());
                $this->eventDispatcher->dispatch($event);
            }
        }
        if ($rate != null) {
            if (is_array($rate)) {
                $rate = $rate[0];
            }
        }
        $deal = $this->getDoctrine()->getRepository(Deal::class)->find($id);
        $deal->decrementRateValue();
        $rate->setIdDeal($id);
        $rate->setValue(-1);
        $rate->setIdUser($this->getUser()->getId());
        $this->getDoctrine()->getManager()->persist($deal);
        $this->getDoctrine()->getManager()->persist($rate);
        $this->getDoctrine()->getManager()->flush();
        
        if ($deal instanceof PromoCode) {
            return $this->redirectToRoute('promocode', array("promocodeid" => $id));
        }

        if ($deal instanceof Plan) {
            return $this->redirectToRoute('plan', array("planid" => $id));
        }
        
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("deal/saved/{userId}", defaults={"userId" = null}, name="deal_display_saved_user")
     */
    public function displaySavecDealsByUser($userId)
    {
        if ($userId == null) {
            return $this->redirectToRoute('home');
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        if ($user == null) {
            return $this->redirectToRoute("home");
        }
        $deals =  $user->getSavedDeals();
        return $this->render('deal/savedDeals.html.twig', [
            "deals" => $deals,
            "type" => Deal::TYPE_NAME
        ]);
    }

    /**
     * @Route("deal/signal/{dealId}", defaults={"dealId" = null}, name="deal_signal")
     */
    public function signalDeal($dealId, MailerInterface $mailer)
    {
        if ($dealId == null) {
            return new Response("Deal not found", 404);
        }
        $deal = $this->getDoctrine()->getRepository(Deal::class)->find($dealId);
        if ($deal == null) {
            return new Response("Deal not found", 404);
        }
        $message = new TemplatedEmail();
        $message->from($this->getUser()->getEmail())
            ->to('admin@admin.com')
            ->subject('Signalement de deal')
            ->htmlTemplate('mails/signalement.html.twig')
            ->context([
                'username' => $this->getUser()->getUserName(),
                'deal' => $deal,
                'dealId' => $dealId
            ]);

        $mailer->send($message);
        return $this->redirectToRoute('home');
    }
}
