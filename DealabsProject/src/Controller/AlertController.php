<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Entity\User;
use App\Form\AlertType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AlertController extends AbstractController
{
    /**
     * @Route("/alert/create", name="create_alert")
     */
    public function createAlert(Request $request)
    {
        $alert = new Alert();
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $this->getUser()) {
            $this->getUser()->addAlert($alert);
            $em = $this->getDoctrine()->getManager();
            $em->persist($alert);
            $em->flush();
            return $this->redirectToRoute('user_profil');
        }
        return $this->render('alert/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/alert/delete/{alertid}",defaults={"alertid" = null}, name="delete_alert")
     */
    public function deleteAlert($alertid)
    {
        if ($alertid != null && $this->getUser()) {
            $alert = $this->getDoctrine()->getRepository(Alert::class)->find($alertid);
            $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser()->getId());
            if ($alert != null) {
                $user->removeAlert($alert);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }
        return $this->redirectToRoute('user_profil');
    }
}
