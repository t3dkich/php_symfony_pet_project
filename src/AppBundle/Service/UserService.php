<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 25.12.2018 г.
 * Time: 16:06
 */

namespace AppBundle\Service;


use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Entity\UserDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @property EntityManagerInterface entityManager
 * @property ContainerInterface container
 * @property Security security
 */
class UserService implements UserServiceInterface
{

    /**
     * UserService constructor.
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(Security $security ,EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->security = $security;
    }

    /**
     * @param User $user
     */
    public function register(User $user)
    {
        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPassword());

        $role = $this->entityManager->getRepository(Role::class)
            ->findBy([
                'name' => 'ROLE_USER'
            ]);

        $user->setPassword($password);
        $user->addRoles($role[0]);


        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function checkIfExists(User $user)
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findBy(['email' => $user->getEmail()]) === [] ? null : true;
    }

    /**
     * @return User|object|null
     */
    public function getHelper()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->find(-1);
    }

    /**
     * @param string $email
     * @return User|object|null
     */
    public function getByEmail(string $email)
    {

        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }

    public function details(UserDetails $userDetails)
    {
        $details = $this->entityManager->getRepository(UserDetails::class)
            ->findOneBy(['user' => $this->security->getUser()]);

        if (null === $details) {
            $this->entityManager->persist($userDetails);
            $this->entityManager->flush();
        } else {
            $this->entityManager->merge($userDetails->setId($details->getId()));
            $this->entityManager->flush();
        }
    }

    public function getAll()
    {

        return $this->entityManager
            ->getRepository(User::class)
            ->findAll();
    }

    public function getById($id)
    {

        return $this->entityManager
            ->getRepository(User::class)
            ->find($id) ?? null;
    }
}