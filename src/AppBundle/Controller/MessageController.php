<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
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
     * @throws \Exception
     */
    public function sendMessage(Request $request, $offerId)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);
        $offer = $this->offerService->getById($offerId);

        $validator = $this->get('validator');
        $errors = $validator->validate($message);

        if (count($errors) > 0) {

            return $this->render('offer/details.html.twig', [
                'id' => $offerId,
                'offer' => $offer,
                'messages' => $this->messageService->findByOfferId($offerId),
                'form' => $form->createView(),
                'errors' => $errors
            ]);
        }

        $this->messageService->addMessage($message, $offer, $this->getUser());

        return $this->redirectToRoute('offer_details', [
            'id' => $offerId,
            'offer' => $offer,
            'messages' => $this->messageService->findByOfferId($offerId),
            'form' => $form->createView(),
            'errors' => []
        ]);

    }

}
