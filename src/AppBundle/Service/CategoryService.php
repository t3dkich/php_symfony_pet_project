<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 25.12.2018 г.
 * Time: 18:05
 */

namespace AppBundle\Service;


use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
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
            ->getRepository(Category::class)
            ->findAll();
    }
}