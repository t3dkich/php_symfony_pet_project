<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Form\MessageType;
use AppBundle\Service\MessageServiceInterface;
use AppBundle\Service\OfferServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @property OfferServiceInterface offerService
 * @property MessageServiceInterface messageService
 */
class MessageController extends Controller
{

    /**
     * OfferController constructor.
     * @param OfferServiceInterface $offerService
     * @param MessageServiceInterface $messageService
     */
    public function __construct(
        OfferServiceInterface $offerService,
        MessageServiceInterface $messageService)
    {
        $this->offerService = $offerService;
        $this->messageService = $messageService;
    }

    /**
     * @Route("/message/{offerId}/send", name="send_message")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $offerId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sendMessage(Request $request, $offerId)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);
        $offer = $this->offerService->getById($offerId);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->messageService->addMessage($message, $offer, $this->getUser());

            return $this->redirectToRoute('offer_details', [
                'id' => $offerId,
                'offer' => $offer,
                'messages' => $this->messageService->findByOfferId($offerId)
            ]);
        }

        return $this->redirectToRoute('offer_details', [
            'id' => $offerId,
            'offer' => $offer,
            'messages' => $this->messageService->findByOfferId($offerId),
            'form' => $form->createView()
        ]);
    }

}
