<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @Route("/messages/{endId}", name="messages")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $endId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $endId)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $endUser = $this->getDoctrine()->getRepository(User::class)
            ->find($endId);

        $form->handleRequest($request);
         if ($form->isSubmitted()) {
            $message
                ->setStartUser($this->getUser())
                ->setEndUser($endUser);

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->render('message/my_messages.html.twig', [
                'endUser' => $endUser
            ]);
        }

        return $this->render('message/send_to.html.twig', [
            'endUser' => $endUser
        ]);
    }
}
