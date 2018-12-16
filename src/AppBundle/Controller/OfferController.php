<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Animal;
use AppBundle\Entity\AnimalCategory;
use AppBundle\Entity\Category;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use AppBundle\Form\AnimalType;
use AppBundle\Form\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    /**
     * @Route("/offer/create", name="offer_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        $offer = new Offer();
        $animal = new Animal();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $animalCategories = $this->getDoctrine()->getRepository(AnimalCategory::class)->findAll();

        $form = $this->createForm(OfferType::class, $offer);
        $animalForm = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $animalForm->handleRequest($request);

        if ($animalForm->isSubmitted() && $form->isSubmitted()) {
            $helperUser = $this->getDoctrine()->getRepository(User::class)->find(-1);
            $em = $this->getDoctrine()->getManager();
            $offer
                ->setAnimal($animal)
                ->setDateAdded(new \DateTime('now'))
                ->setUser($this->getUser())
                ->setEndPointUser($helperUser)
                ->setState('open');
            $em->persist($offer);
            $em->flush();

            return $this->redirectToRoute('homepage');

        }

        return $this->render('offer/create.html.twig', [
            'categories' => $categories,
            'animalCategories' => $animalCategories
        ]);
    }
}
