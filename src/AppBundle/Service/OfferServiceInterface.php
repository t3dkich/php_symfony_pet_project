<?php
/**
 * Created by PhpStorm.
 * User: t3dki
 * Date: 30.12.2018 г.
 * Time: 20:13
 */

namespace AppBundle\Service;


use AppBundle\Entity\Animal;
use AppBundle\Entity\Offer;
use AppBundle\Entity\User;

interface OfferServiceInterface
{
    public function getMuSortedBy(User $user, $order);
    public function getAllSortedBy($argument);
    public function paginate($page, array $offers);
    public function create(Offer $offer, User $user, Animal $animal, $picture, User $helperUser);
    public function getById(int $id);
    public function edit(Animal $animal, Offer $existingOffer, Offer $offer);

    public function sellToUser(User $user, Offer $offer);
    public function cancel(string $id, int $endPointUserId = null);
    public function getOneOfEachType();
    public function reOpen(string $id);
}