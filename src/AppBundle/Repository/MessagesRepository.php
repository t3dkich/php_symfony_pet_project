<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Offer;

/**
 * MessagesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessagesRepository extends \Doctrine\ORM\EntityRepository
{
    public function groupForOfferByUsers(Offer $offer)
    {

        return $this->createQueryBuilder('m')
            ->where('m.offer = :offer')
            ->setParameter('offer', $offer)
            ->groupBy('m.startUser')
            ->getQuery()
            ->getResult();
    }
}
