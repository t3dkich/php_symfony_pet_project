<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 3.1.2019 г.
 * Time: 15:45
 */

namespace AppBundle\Service;


use AppBundle\Entity\Message;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;

interface MessageServiceInterface
{
    public function findByOfferId($offerId);

    public function addMessage(Message $message, Offer $offer, User $getUser);

    public function getGroupedMessages(Offer $offer);
}