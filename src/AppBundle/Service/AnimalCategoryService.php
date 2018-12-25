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

class AnimalCategoryService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAll()
    {

        return $this->entityManager
            ->getRepository(AnimalCategory::class)
            ->findAll();
    }
}