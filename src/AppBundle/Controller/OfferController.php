<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Animal;
use AppBundle\Entity\AnimalCategory;
use AppBundle\Entity\Category;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use AppBundle\Form\AnimalType;
use AppBundle\Form\OfferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    /**
     * @Route("/offer/edit/{id}", name="offer_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction(Request $request, $id)
    {
        $offer = new Offer();
        $animal = new Animal();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $animalCategories = $this->getDoctrine()->getRepository(AnimalCategory::class)->findAll();
        $offerExist = $this->getDoctrine()->getRepository(Offer::class)
            ->find($id);

        if (!$offerExist) {
            return $this->render('default/index.html.twig');
        }

        $form = $this->createForm(OfferType::class, $offer);
        $animalForm = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $animalForm->handleRequest($request);

        if ($animalForm->isSubmitted() && $form->isSubmitted()) {
            $helperUser = $this->getDoctrine()->getRepository(User::class)->find(-1);
            $em = $this->getDoctrine()->getManager();

            if (!$animal->getPicture()) {
                $animal->setPicture($offerExist->getAnimal()->getPicture());
            }

            /** @var Animal $animalEdited */
            $animalEdited = $offerExist->getAnimal();
            $animalEdited
                ->setName($animal->getName())
                ->setBreed($animal->getBreed())
                ->setDescription($animal->getDescription())
                ->setPicture($animal->getPicture())
                ->setAge($animal->getAge());

            $offerExist
                ->setTitle($offer->getTitle())
                ->setPrice($offer->getPrice())
                ->setCategory($offer->getCategory())
                ->setAnimalType($offer->getAnimalType())
                ->setAnimal($animalEdited)
                ->setDateAdded(new \DateTime('now'))
                ->setUser($this->getUser())
                ->setEndPointUser($helperUser)
                ->setState('open');

            /** @var UploadedFile $picture */
            $picture = $animalEdited->getPicture();
            if ($picture && gettype($picture) !== 'string') {
                $fileName = md5(uniqid()) . '.' . $picture->guessExtension();
                $picture->move($this->getParameter('image_directory'), $fileName);
                $offerExist->getAnimal()->setPicture($fileName);
            }

            $em->persist($offerExist);
            $em->flush();

            return $this->redirectToRoute('homepage');

        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offerExist,
            'categories' => $categories,
            'animalCategories' => $animalCategories
        ]);
    }

    /**
     * @Route("/offer/my", name="offers_my")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showMyOffers()
    {
        $myOffers = $this->getDoctrine()->getRepository(Offer::class)
            ->findBy(['user' => $this->getUser()]);

        return $this->render('offer/my.html.twig', [
            'myOffers' => $myOffers
        ]);

    }

    /**
     * @Route("/offer/all", name="offers_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllOffersAction(Request $request)
    {
        $offers = $this->getDoctrine()->getRepository(Offer::class)->findBy([
            'state' => 'open'
        ]);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $offers, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );

        // parameters to template
        return $this->render('offer/all.html.twig', array('pagination' => $pagination));
    }

        //return $this->render('offer/all.html.twig', ['offers' => $offers]);

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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

            /** @var UploadedFile $picture */
            $picture = $form->getData()->getAnimal()->getPicture();
            if ($picture) {
                $fileName = md5(uniqid()) . '.' . $picture->guessExtension();
                $picture->move($this->getParameter('image_directory'), $fileName);
                $offer->getAnimal()->setPicture($fileName);
            }

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

