<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Deal;
use App\Form\GroupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    /**
     * @Route("/group{id}",defaults={"id" = null}, name="group_deals")
     */
    public function index($id)
    {
        $group = $this->getDoctrine()->getRepository(Group::class)->find($id);
        return $this->render('group/dealsListByGroup.html.twig', [
            'deals' => $group->getDeals(),
            'group' => $group,
            'type' => Deal::TYPE_NAME
        ]);
    }

    /**
     * @Route("/group/create", name="create_group")
     */
    public function createGroup(Request $request)
    {
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
