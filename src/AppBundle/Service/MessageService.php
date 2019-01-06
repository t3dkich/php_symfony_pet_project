<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 3.1.2019 г.
 * Time: 15:46
 */

namespace AppBundle\Service;


use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property EntityManagerInterface entityManager
 * @property ContainerInterface container
 */
class MessageService implements MessageServiceInterface
{
    /**
     * MessageService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * @param $offerId
     * @return Message[]|Offer[]|\AppBundle\Entity\Role[]|User[]|array
     */
    public function findByOfferId($offerId)
    {

        return $this->entityManager
            ->getRepository(Message::class)
            ->findBy([
                'offer' => $offerId
            ]);
    }

    /**
     * @param Message $message
     * @param Offer $offer
     * @param User $getUser
     */
    public function addMessage(Message $message, Offer $offer, User $getUser)
    {
        $message
            ->setOffer($offer)
            ->setStartUser($getUser);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    /**
     * @param Offer $offer
     * @return array
     */
    public function getGroupedMessages(Offer $offer)
    {

        return $this->entityManager
            ->getRepository(Message::class)
            ->groupForOfferByUsers($offer);
    }
}