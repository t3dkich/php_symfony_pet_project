<?php

namespace AppBundle\Service;


use AppBundle\Entity\Animal;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OfferService implements OfferServiceInterface
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

    public function getMy(User $user, $order)
    {
        switch ($order) {
            case 'all':
                return $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'user' => $user
                    ]);

            case 'for_sale':
                return $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'user' => $user,
                        'state' => 'open'
                    ]);

            case 'sold':
                return $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'user' => $user,
                        'state' => 'sold'
                    ]);

            case 'cancelled':
                return $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'user' => $user,
                        'state' => 'cancelled'
                    ]);
                break;

        }

        return $this->entityManager
            ->getRepository(Offer::class)
            ->findBy([
                'user' => $user
            ]);
    }

    public function getAllSortedBy($order)
    {
        switch ($order) {
            case 'date':
                return $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'state' => 'open'
                    ], [
                        'dateAdded' => 'ASC'
                    ]);
            case 'popular':
                $offers = $this->entityManager
                    ->getRepository(Offer::class)
                    ->findBy([
                        'state' => 'open'
                    ]);

                usort($offers, function ($a, $b) {
                    /** @var Offer $a
                     * @var Offer $b
                     */
                    return count($b->getBidders()) <=> count($a->getBidders());
                });

                return $offers;

        }

        return $this->entityManager->getRepository(Offer::class)->findBy([
            'state' => 'open',
        ]);
    }

    public function paginate($page, array $offers)
    {
        $paginator = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $offers, /* query NOT result */
            $page/*page number*/,
            6/*limit per page*/
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

    public function getById(int $id)
    {
        return $this->entityManager->getRepository(Offer::class)->find($id);
    }

    /**
     * @param Animal $animal
     * @param Offer $existingOffer
     * @param Offer $offer
     * @throws \Exception
     */
    public function edit(Animal $animal, Offer $existingOffer, Offer $offer)
    {
        if (!$animal->getPicture()) {
            $animal->setPicture($existingOffer->getAnimal()->getPicture());
        }

        /** @var Animal $animalEdited */
        $animalEdited = $existingOffer->getAnimal();
        $animalEdited
            ->setName($animal->getName())
            ->setBreed($animal->getBreed())
            ->setDescription($animal->getDescription())
            ->setPicture($animal->getPicture())
            ->setAge($animal->getAge());

        $existingOffer
            ->setTitle($offer->getTitle())
            ->setPrice($offer->getPrice())
            ->setCategory($offer->getCategory())
            ->setAnimalType($offer->getAnimalType())
            ->setAnimal($animalEdited);

        $catId = $existingOffer->getCategory()->getId();

        if ($catId === 3 || $catId === 4) {
            $existingOffer->setPrice(null);
        }

        /** @var UploadedFile $picture */
        $picture = $animalEdited->getPicture();
        if ($picture && gettype($picture) !== 'string') {
            $fileName = md5(uniqid()) . '.' . $picture->guessExtension();
            $picture->move($this->container->getParameter('image_directory'), $fileName);
            $existingOffer->getAnimal()->setPicture($fileName);
        }

        $this->entityManager->merge($existingOffer);
        $this->entityManager->flush();
    }

    public function sellToUser($user, $offer)
    {
        $offer->setEndPointUser($user)
            ->setState('sold');

        $this->entityManager->merge($offer);
        $this->entityManager->flush();
    }

    public function cancel(string $id)
    {
        $offer = $this->entityManager->getRepository(Offer::class)->find($id);
        $offer->setState('cancelled');
        $this->entityManager->merge($offer);
        $this->entityManager->flush();

    }
}