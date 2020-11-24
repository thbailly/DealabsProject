<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\Deal;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Alert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user/add/{dealid}", name="user_saveDeal")
     */
    public function saveDealToUserList($dealid)
    {
        $doctrine = $this->getDoctrine();
        $deal = $doctrine->getRepository(Deal::class)->find($dealid);
        $user = $doctrine->getRepository(User::class)->find($this->getUser()->getId());

        $user->addDeal($deal);

        $doctrine->getManager()->persist($user);
        $doctrine->getManager()->persist($deal);
        $doctrine->getManager()->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/user/profil", name="user_profil")
     */
    public function displayUserProfil()
    {
        if ($this->getUser() != null) {
            $doctrine = $this->getDoctrine();
            $dealsSaved =  $this->getUser()->getSavedDeals();
            $dealsPublished = $doctrine->getRepository(Deal::class)->findAllByAuthorId($this->getUser()->getId());
            $commentsPublished = $doctrine->getRepository(Comment::class)->findAllByAuthorId($this->getUser()->getId());
            $hotestDeal = $doctrine->getRepository(Deal::class)->findHotestByAuthorId($this->getUser()->getId());
            $dealsHot = $doctrine->getRepository(Deal::class)->findAllOrderByHotAndByAuthorId($this->getUser()->getId());
            $dealsPublishedLastYear = $doctrine->getRepository(Deal::class)->avgPostedByAuthorIdUnderOneYear($this->getUser()->getId());
            $userBadges = [];
            $userRates = $this->getUser()->getNbVote();
            foreach ($this->getUser()->getBadges() as $badge) {
                array_push($userBadges, $badge->getTitle());
            }
            $allBadges = [Badge::BADGE_SURVEILLANT,Badge::BADGE_COBAYE,Badge::BADGE_RAPPORT_DE_STAGE];
            $alerts =  $doctrine->getRepository(Alert::class)->findAllByUserId($this->getUser()->getId());

            return $this->render("user/profil.html.twig", [
                'user' => $this->getUser(),
                'dealsPublished' => $dealsPublished,
                'dealsSaved' => $dealsSaved,
                'type' => Deal::TYPE_NAME,
                'statsDealsPosted' => count($dealsPublished),
                'statsCommentsPosted' => count($commentsPublished),
                'statsHotestDeal' => $hotestDeal ? $hotestDeal[0] : null,
                'statsAvgDealsPosted' => $dealsPublishedLastYear ? $dealsPublishedLastYear[0][1] : null,
                'statsPercentageDealsPostedBeingHot' => count($dealsPublished) ? ((count($dealsHot) / count($dealsPublished)) * 100) : 0,
                'statRates' => $userRates,
                'userBadges' => $userBadges,
                'allBadges' => $allBadges,
                'dealsFromAlerts' => [],
                'alerts' => $alerts
            ]);
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/user/delete/{userid}",defaults={"userid" = null}, name="user_delete")
     */
    public function deleteUser($userid, UserPasswordEncoderInterface $passwordEncoder)
    {
        if ($userid != null) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($userid);
            if ($user != null) {
                $faker = Faker\Factory::create('fr_FR');
                $user->setEmail($faker->email);
                $user->setUsername($faker->name);
                $password = $passwordEncoder->encodePassword($user, $faker->word);
                $user->setPassword($password);
                $user->setActive(false);

                $doctrine = $this->getDoctrine();
                $doctrine->getManager()->persist($user);
                $doctrine->getManager()->flush();

                return $this->redirectToRoute('app_logout');
            }
            return $this->redirectToRoute('home');
            return $this->redirectToRoute("user_profil", ['id' => $userid]);
        }
        return $this->redirectToRoute('home');
    }
}
