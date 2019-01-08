<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserDetails
 *
 * @ORM\Table(name="user_details")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserDetailsRepository")
 */
class UserDetails
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Must have last name")
     * @Assert\Regex(
     *     pattern="/[a-zA-Z]{1,25}/",
     *     match=true,
     *     message="Your name must have between 1 and 25 english letters only"
     * )
     * @ORM\Column(name="last_name", type="string", length=25)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Must have town")
     * @Assert\Regex(
     *     pattern="/[a-zA-Z]{2,50}/",
     *     match=true,
     *     message="Your name must have between 2 and 50 english letters only"
     * )
     * @ORM\Column(name="town", type="string", length=50)
     */
    private $town;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Must have street address")
     * @Assert\Regex(
     *     pattern="/[a-zA-Z0-9]{5,75}/",
     *     match=true,
     *     message="Your street address must have between 5 and 75 english letters and numbers"
     * )
     *
     * @ORM\Column(name="street_address", type="string", length=75)
     */
    private $streetAddress;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Must have age")
     * @Assert\Range(
     *      min = 18,
     *      max = 150,
     *      minMessage = "You must be at least {{ limit }} years old to enter",
     *      maxMessage = "You cannot be more than {{ limit }} years old to enter"
     * )
     *
     * @ORM\Column(name="age", type="smallint")
     */
    private $age;

    /**
     * @var string
     *  @Assert\NotBlank(message="Must have user details")
     * @Assert\Length(
     *      min = 2,
     *      max = 999,
     *      minMessage = "Your details must be at least {{ limit }} characters long",
     *      maxMessage = "Your details cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(name="details", type="text")
     */
    private $details;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="details")
     */
    private $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserDetails
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return UserDetails
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return UserDetails
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set streetAddress
     *
     * @param string $streetAddress
     *
     * @return UserDetails
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return UserDetails
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return UserDetails
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }
}

