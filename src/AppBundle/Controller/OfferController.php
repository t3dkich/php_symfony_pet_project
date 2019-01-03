<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Animal;
use AppBundle\Entity\Offer;
use AppBundle\Form\AnimalType;
use AppBundle\Form\OfferType;
use AppBundle\Service\AnimalTypeServiceInterface;
use AppBundle\Service\CategoryServiceInterface;
use AppBundle\Service\MessageServiceInterface;
use AppBundle\Service\OfferServiceInterface;
use AppBundle\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property AnimalTypeServiceInterface animalTypeService
 * @property OfferServiceInterface offerService
 * @property UserServiceInterface userService
 * @property CategoryServiceInterface categoryService
 * @property MessageServiceInterface messageService
 */
class OfferController extends Controller
{
    /**
     * OfferController constructor.
     * @param OfferServiceInterface $offerService
     * @param UserServiceInterface $userService
     * @param AnimalTypeServiceInterface $animalTypeService
     * @param CategoryServiceInterface $categoryService
     * @param MessageServiceInterface $messageService
     */
    public function __construct(
        OfferServiceInterface $offerService,
        UserServiceInterface $userService,
        AnimalTypeServiceInterface $animalTypeService,
        CategoryServiceInterface $categoryService,
        MessageServiceInterface $messageService)
    {
        $this->offerService = $offerService;
        $this->userService = $userService;
        $this->animalTypeService = $animalTypeService;
        $this->categoryService = $categoryService;
        $this->messageService = $messageService;
    }

    /**
     * @Route("/offer/cancel/{id}", name="offer_cancel")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function CancelAction($id)
    {
        $this->offerService->cancel($id);

        return $this->redirectToRoute('offers_all');
    }

    /**
     * @Route("/offer/sell_close/{id}", name="sell_close")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sellCloseAction(Request $request, $id)
    {
        $offer = $this->offerService->getById($id);

        if ($request->isMethod('post') && $request->request->get('user')['email']) {

            $email = $request->request->get('user')['email'];

            if (null !== $email) {
                $user = $this->userService->getByEmail($email);

                if (null !== $user) {
                    $this->offerService->sellToUser($user, $offer);
                } else {
                    $this->addFlash('info',
                        "Username with email " . $email . " doesn't exist!");

                    return $this->render('offer/sell_close.html.twig', [
                        'offer' => $offer,
                        'messages' => $this->messageService->getGroupedMessages($offer)
                    ]);
                }

            }

            return $this->redirectToRoute('offers_all');
        }

        return $this->render('offer/sell_close.html.twig', [
            'offer' => $offer,
            'messages' => $this->messageService->getGroupedMessages($offer)
        ]);
    }

    /**
     * @Route("/offer/details/{id}", name="offer_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction($id)
    {

        return $this->render('offer/details.html.twig', [
            'offer' => $this->offerService->getById($id),
            'messages' => $this->messageService->findByOfferId($id)
        ]);
    }

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
        [$categories, $animalCategories, $existingOffer] = [
            $this->categoryService->getAll(),
            $this->animalTypeService->getAll(),
            $this->offerService->getById($id)
        ];

        if (!$existingOffer) {

            return $this->render('default/index.html.twig');
        }

        $form = $this->createForm(OfferType::class, $offer);
        $animalForm = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $animalForm->handleRequest($request);

        if ($animalForm->isSubmitted() && $form->isSubmitted()) {

            $this->offerService->edit($animal, $existingOffer, $offer, $this->userService->getHelper(), $this->getUser());

            return $this->redirectToRoute('homepage');

        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $existingOffer,
            'categories' => $categories,
            'animalCategories' => $animalCategories
        ]);
    }

    /**
     * @Route("/offer/my", name="offers_my")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myAction(Request $request)
    {
        $pagination = $this->offerService
            ->paginate($request->query->getInt('page', 1), $this->offerService->getMy($this->getUser()));

        return $this->render('offer/my.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/offer/all", name="offers_all")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allAction(Request $request)
    {
        $pagination = $this->offerService
            ->paginate($request->query->getInt('page', 1), $this->offerService->getAllOpen());

        return $this->render('offer/all.html.twig', [
            'pagination' => $pagination
        ]);
    }

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
        [$categories, $animalCategories] = [
            $this->categoryService->getAll(), $this->animalTypeService->getAll()
        ];

        $form = $this->createForm(OfferType::class, $offer);
        $animalForm = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);
        $animalForm->handleRequest($request);

        if ($animalForm->isSubmitted() && $form->isSubmitted()) {

            $this->offerService->create($offer, $this->getUser(), $animal, $animalForm->getData()->getPicture(), $this->userService->getHelper());

            return $this->redirectToRoute('homepage');
        }

        return $this->render('offer/create.html.twig', [
            'categories' => $categories,
            'animalCategories' => $animalCategories
        ]);
    }
}

