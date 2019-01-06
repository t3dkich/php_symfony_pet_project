<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 2.1.2019 г.
 * Time: 16:42
 */

namespace AppBundle\Service;


use AppBundle\Entity\User;
use AppBundle\Entity\UserDetails;

interface UserServiceInterface
{
    public function register(User $user);
    public function checkIfExists(User $user);
    public function getHelper();
    public function getByEmail(string $email);

    public function details(UserDetails $userDetails);
}