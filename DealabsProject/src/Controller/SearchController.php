<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/deal/search", name="deal_search")
     */
    public function dealSearch(Request $request)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $criteria = $form->getData()['search'];
            $deals = $this->getDoctrine()->getRepository(Deal::class)->findAllByCriteria($criteria);
            return $this->render('search/searchResult.html.twig', [
                'deals' => $deals,
                'type' => Deal::TYPE_NAME
            ]);
        }
        return $this->render('search/search.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
