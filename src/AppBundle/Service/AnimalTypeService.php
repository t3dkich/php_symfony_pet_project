<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 25.12.2018 г.
 * Time: 18:07
 */

namespace AppBundle\Service;


use AppBundle\Entity\AnimalCategory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @property EntityManagerInterface entityManager
 */
class AnimalTypeService implements AnimalTypeServiceInterface
{
    /**
     * AnimalTypeService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return AnimalCategory[]|\AppBundle\Entity\Category[]|array
     */
    public function getAll()
    {

        return $this->entityManager
            ->getRepository(AnimalCategory::class)
            ->findAll();
    }
}