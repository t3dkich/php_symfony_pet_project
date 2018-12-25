<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Animal;
use AppBundle\Entity\AnimalCategory;
use AppBundle\Entity\Category;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use AppBundle\Form\AnimalType;
use AppBundle\Form\OfferType;
use AppBundle\Service\AnimalCategoryService;
use AppBundle\Service\CategoryService;
use AppBundle\Service\OfferService;
use AppBundle\Service\UserService;
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
     * @param OfferService $offerService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myAction(OfferService $offerService)
    {

        return $this->render('offer/my.html.twig', [
            'myOffers' => $offerService->getMy($this->getUser())
        ]);
    }

    /**
     * @Route("/offer/all", name="offers_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param OfferService $offerService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allAction(Request $request, OfferService $offerService)
    {
        $pagination = $offerService
            ->paginate($request->query->getInt('page', 1), $offerService->getAllOpen());

        return $this->render('offer/all.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/offer/create", name="offer_create")
     * @param Request $request
     * @param CategoryService $categoryService
     * @param AnimalCategoryService $animalCategoryService
     * @param UserService $userService
     * @param OfferService $offerService
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request,
                                 CategoryService $categoryService,
                                 AnimalCategoryService $animalCategoryService,
                                 UserService $userService,
                                 OfferService $offerService)
    {
        $offer = new Offer();
        $animal = new Animal();
        [$categories, $animalCategories] = [
            $categoryService->getAll(), $animalCategoryService->getAll()
        ];

        $form = $this->createForm(OfferType::class, $offer);
        $animalForm = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $animalForm->handleRequest($request);

        if ($animalForm->isSubmitted() && $form->isSubmitted()) {

            $helperUser = $userService->getHelper();
            $picture = $animalForm->getData()->getPicture();
            $offerService->create($offer, $this->getUser(), $animal, $picture, $helperUser);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('offer/create.html.twig', [
            'categories' => $categories,
            'animalCategories' => $animalCategories
        ]);
    }
}

