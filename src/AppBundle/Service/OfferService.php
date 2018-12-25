<?php

namespace AppBundle\Service;


use AppBundle\Entity\Animal;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OfferService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    public function getMy(User $user)
    {
        return $this->entityManager
            ->getRepository(Offer::class)
            ->findBy([
                'user' => $user
            ]);
    }

    public function getAllOpen()
    {

        return $this->entityManager->getRepository(Offer::class)->findBy([
            'state' => 'open'
        ]);
    }

    public function paginate($page, array $offers)
    {
        $paginator = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $offers, /* query NOT result */
            $page/*page number*/,
            4/*limit per page*/
        );

        return $pagination;
    }

    /**
     * @param Offer $offer
     * @param User $user
     * @param Animal $animal
     * @param $picture
     * @param User $helperUser
     * @throws \Exception
     */
    public function create(Offer $offer, User $user, Animal $animal, $picture, User $helperUser)
    {
        $offer
            ->setAnimal($animal)
            ->setDateAdded(new \DateTime('now'))
            ->setUser($user)
            ->setEndPointUser($helperUser)
            ->setState('open');

        /** @var UploadedFile $picture */
        if ($picture) {
            $fileName = md5(uniqid()) . '.' . $picture->guessExtension();
            $picture->move($this->container->getParameter('image_directory'), $fileName);
            $offer->getAnimal()->setPicture($fileName);
        }

        $this->entityManager->persist($offer);
        $this->entityManager->flush();
    }
}