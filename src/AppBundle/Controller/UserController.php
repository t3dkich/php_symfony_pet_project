<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Offer;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\UserDetails;
use AppBundle\Form\UserDetailsType;
use AppBundle\Form\UserType;
use AppBundle\Service\AnimalTypeServiceInterface;
use AppBundle\Service\CategoryServiceInterface;
use AppBundle\Service\MessageServiceInterface;
use AppBundle\Service\OfferServiceInterface;
use AppBundle\Service\UserService;
use AppBundle\Service\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property AnimalTypeServiceInterface animalTypeService
 * @property OfferServiceInterface offerService
 * @property UserServiceInterface userService
 * @property CategoryServiceInterface categoryService
 * @property MessageServiceInterface messageService
 */
class UserController extends Controller
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
     * @Route("/user/{id}/detail", name="user_details")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userDetailsAction($id)
    {
        $id = (int) $id;
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()) && $this->getUser()->getId() !== $id) {

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/details.html.twig', [
            'user' => $this->userService->getById($id)
        ]);

    }

    /**
     * @Route("/user/{id}/details_edit", name="user_details_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userDetailsEditAction(Request $request)
    {
        $userDetails = new UserDetails();
        $form = $this->createForm(UserDetailsType::class, $userDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $validator = $this->get('validator');
            $errors = $validator->validate($userDetails);

            if (count($errors) > 0) {

                return $this->render('user/details_edit.html.twig', [
                    'form' => $form->createView(),
                    'user' => $this->getUser(),
                    'formUserDetails' => $userDetails,
                    'errors' => $errors
                ]);
            }
            $this->getUser()->setDetails($userDetails);
            $this->userService->details($userDetails);

            $this->addFlash('info', 'You have successfully edited your profile.');

            return $this->redirectToRoute('user_details', [
                'id' => $this->getUser()->getId()
            ]);
        }

        return $this->render('user/details_edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'formUserDetails' => null,
            'errors' => []
        ]);
    }

    /**
     * @Route("/user/bid/{offerId}", name="user_bid_offer")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $offerId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function bidOnOffer($offerId)
    {
        /** @var Offer $offer */
        $offer = $this->offerService->getById($offerId);
        $user = $this->getUser();
        $isAllowed = true;
        foreach ($offer->getBidders() as $bidder) {
            if ($bidder === $user) $isAllowed = false;
        }

        if ($isAllowed) {
            $offer->setBidders($user);
            $em = $this->getDoctrine()->getManager();
            $em->merge($offer);
            $em->flush();
            $this->addFlash('info',
                "You have successfully send request to get that animal!");
        } else {
            $this->addFlash('infoRed',
                "Already send one request from this user!");
        }

        return $this->redirectToRoute('offer_details', [
            'id' => $offer->getId()
        ]);

    }

    /**
     * @Route("/user/register", name="user_register")
     * @param Request $request
     * @param UserService $userService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, UserService $userService)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $validator = $this->get('validator');
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                    'user' => $user
                ]);
            }

            if (null !== $userService->checkIfExists($user)) {

                $this->addFlash('info',
                    "Username with email " . $user->getEmail() . " already taken!");

                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                    'user' => $user
                ]);
            }

            $userService->register($user);

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'errors' => [],
            'user' => null
        ]);
    }
}
