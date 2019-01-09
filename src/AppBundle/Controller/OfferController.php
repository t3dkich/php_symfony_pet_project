<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Animal;
use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use AppBundle\Form\AnimalType;
use AppBundle\Form\MessageType;
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelAction($id)
    {
        $this->offerService->cancel($id);

        return $this->redirectToRoute('offers_my', [
            'order' => 'cancelled'
        ]);
    }

    /**
     * @Route("/offer/sell_close/{id}", name="sell_close")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sellAction(Request $request, $id)
    {
        /** @var Offer $offer */
        $offer = $this->offerService->getById($id);
        $email = $request->request->get('user')['email'];

        if ($request->isMethod('post') && $email) {

            if (null !== $email) {

                $isMatch = false;
                $user = $this->userService->getByEmail($email);
                foreach ($offer->getBidders() as $bidder) {
                    /** @var User $bidder */
                    if ($bidder->getEmail() === $email) {
                        $isMatch = true;
                    }
                }

                if (null !== $user && $isMatch) {
                    $this->offerService->sellToUser($user, $offer);
                } else {
                    $this->addFlash('info',
                        "Username with email " . $email . " doesn't exist or this user has not bid on that offer");

                    return $this->render('offer/sell_close.html.twig', [
                        'offer' => $offer,
                        'bidders' => $offer->getBidders()
                    ]);
                }

            }

            return $this->redirectToRoute('offers_my', [
                'order' => 'sold'
            ]);
        }

        return $this->render('offer/sell_close.html.twig', [
            'offer' => $offer,
            'bidders' => $offer->getBidders()
        ]);
    }

    /**
     * @Route("/offer/details/{id}", name="offer_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function detailsAction($id)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        return $this->render('offer/details.html.twig', [
            'offer' => $this->offerService->getById($id),
            'messages' => $this->messageService->findByOfferId($id),
            'form' => $form->createView(),
            'errors' => []
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

            $validator = $this->get('validator');
            $animalErrors = $validator->validate($animal);
            $offerErrors = $validator->validate($offer);

            if (count($animalErrors) > 0 || count($offerErrors) > 0) {
                if (count($animalErrors) > 1 && $animalErrors[0]->getPropertyPath() !== 'picture') {
                    return $this->render('offer/edit.html.twig', [
                        'offer' => $existingOffer,
                        'categories' => $categories,
                        'animalCategories' => $animalCategories,
                        'animalForm' => $animalForm->createView(),
                        'form' => $form->createView(),
                        'animalErr' => $animalErrors,
                        'offerErr' => $offerErrors
                    ]);
                }
            }

            $this->offerService->edit($animal, $existingOffer, $offer);

            return $this->redirectToRoute('offers_my', [
                'order' => 'all'
            ]);

        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $existingOffer,
            'categories' => $categories,
            'animalCategories' => $animalCategories,
            'animalForm' => $animalForm->createView(),
            'form' => $form->createView(),
            'animalErr' => [],
            'offerErr' => []
        ]);
    }

    /**
     * @Route("/offer/my/{order}", name="offers_my")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myAction(Request $request, $order)
    {
        $pagination = $this->offerService
            ->paginate($request->query->getInt('page', 1), $this->offerService->getMuSortedBy($this->getUser(), $order));

        return $this->render('offer/my.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/offer/all/{order}", name="offers_all")
     *
     * @param Request $request
     * @param $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allAction(Request $request, $order)
    {
        $pagination = $this->offerService
            ->paginate($request->query->getInt('page', 1), $this->offerService->getAllSortedBy($order));

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

            $validator = $this->get('validator');
            $animalErrors = $validator->validate($animal);
            $offerErrors = $validator->validate($offer);

            if (count($animalErrors) > 0 || count($offerErrors) > 0) {

                return $this->render('offer/create.html.twig', [
                    'categories' => $categories,
                    'animalCategories' => $animalCategories,
                    'form' => $form->createView(),
                    'animalForm' => $animalForm->createView(),
                    'animalErr' => $animalErrors,
                    'offerErr' => $offerErrors,
                    'animal' => $animal,
                    'offer' => $offer
                ]);
            }

            $this->offerService->create($offer, $this->getUser(), $animal, $this->userService->getHelper());

            $this->addFlash('info', 'You have successfully created new offer.');

            return $this->redirectToRoute('offers_my', [
                'order' => 'all'
            ]);
        }

        return $this->render('offer/create.html.twig', [
            'categories' => $categories,
            'animalCategories' => $animalCategories,
            'form' => $form->createView(),
            'animalForm' => $animalForm->createView(),
            'offerErr' => [],
            'animalErr' => [],
            'animal' => null,
            'offer' => null
        ]);
    }
}

