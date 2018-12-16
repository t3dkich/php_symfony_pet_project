<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    /**
     * @Route("/all_offers")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var Offer[] $offers */
        $offers = $this->getDoctrine()->getRepository(Offer::class)
            ->findAll();


        return $this->render('offer/create.html.twig', [
            'offer' => $offers[0]
        ]);
    }
}
