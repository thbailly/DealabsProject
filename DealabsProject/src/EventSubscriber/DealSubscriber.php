<?php


namespace App\EventSubscriber;

use App\Entity\Badge;
use App\Entity\Deal;
use App\Entity\User;
use App\Event\DealCommentEvent;
use App\Event\DealPostEvent;
use App\Event\DealVoteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DealSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            DealVoteEvent::class => 'onDealVote',
            DealPostEvent::class => 'onDealPost',
            DealCommentEvent::class => 'onDealComment'
        ];
    }

    public function onDealVote(DealVoteEvent $event)
    {
        $user = $event->getUser();
        $exist = false;
        if ($user instanceof User) {
            if ($user->getNbVote() >= 10) {
                foreach ($user->getBadges() as $badge) {
                    if ($badge->getTitle() == "Surveillant") {
                        $exist = true;
                    }
                }
                if (!$exist) {
                    $user->addBadge(new Badge("Surveillant"));
                }
            }
        }
    }

    public function onDealPost(DealPostEvent $event)
    {
        $user = $event->getUser();
        $nbPost = $event->getNbpost();
        $exist = false;
        if ($user instanceof User) {
            if ($nbPost >= 10) {
                foreach ($user->getBadges() as $badge) {
                    if ($badge->getTitle() == "Cobaye") {
                        $exist = true;
                    }
                }
                if (!$exist) {
                    $user->addBadge(new Badge("Cobaye"));
                }
            }
        }
    }

    public function onDealComment(DealCommentEvent $event)
    {
        $user = $event->getUser();
        $nbCom = $event->getNbCom();
        $exist = false;
        if ($user instanceof User) {
            if ($nbCom >= 10) {
                foreach ($user->getBadges() as $badge) {
                    if ($badge->getTitle() == "Rapport de stage") {
                        $exist = true;
                    }
                }
                if (!$exist) {
                    $user->addBadge(new Badge("Rapport de stage"));
                }
            }
        }
    }
}
