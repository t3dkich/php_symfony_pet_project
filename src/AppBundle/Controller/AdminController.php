<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\OfferServiceInterface;
use AppBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property UserServiceInterface userService
 * @property OfferServiceInterface offerService
 */
class AdminController extends Controller
{

    /**
     * AdminController constructor.
     * @param UserServiceInterface $userService
     * @param OfferServiceInterface $offerService
     */
    public function __construct(UserServiceInterface $userService, OfferServiceInterface $offerService)
    {
        $this->userService = $userService;
        $this->offerService = $offerService;
    }

    /**
     * @Route("/admin/offer_{offerId}/cancel/{userId}", name="admin_cancel_offer")
     * @param $offerId
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancelOfferAction($offerId, $userId)
    {
        $this->offerService->cancel($offerId, $this->getUser()->getId());

        return $this->redirectToRoute('admin_all_user_offers', [
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/offer_{offerId}/reopen/{userId}", name="admin_reopen_offer")
     * @param $offerId
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reopenOfferAction($offerId, $userId)
    {
        $this->offerService->reOpen($offerId);

        return $this->redirectToRoute('admin_all_user_offers', [
            'userId' => $userId
        ]);
    }

    /**
     * @Route("/admin/user/{userId}/all_offers", name="admin_all_user_offers")
     * @param $userId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function allUserOffersAction($userId, Request $request)
    {
        /** @var User $user */
        $user = $this->userService->getById($userId);
        $userOffers = $this->offerService->getMuSortedBy($user, 'all');

        $pagination = $this->offerService
            ->paginate($request->query->getInt('page', 1), $userOffers);

        return $this->render('admin/user_offers.html.twig', [
            'pagination' => $pagination,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/user_details/{id}", name="admin_user_details")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userDetailsAction($id)
    {
        return $this->render('admin/user_details.html.twig', [
            'user' => $this->userService->getById($id)
        ]);
    }

    /**
     * @Route("/admin_panel", name="admin_panel")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        return $this->render('admin/admin_panel.html.twig', [
            'allUsers' => $this->userService->getAll()
        ]);
    }
}
