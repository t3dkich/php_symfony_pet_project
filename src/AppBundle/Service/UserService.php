<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 25.12.2018 г.
 * Time: 16:06
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserService
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

    public function register(User $user)
    {
        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPassword());
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function checkIfExists(User $user)
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findBy(['email' => $user->getEmail()]) === [] ? null : true;
    }

    public function getHelper()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->find(-1);
    }
}