<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offer
 *
 * @ORM\Table(name="offers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 */
class Offer
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
     *
     * @Assert\NotBlank(message="Offer must have title")
     * @Assert\Length(
     *      min = 5,
     *      max = 25,
     *      minMessage = "Your title must be at least {{ limit }} characters long",
     *      maxMessage = "Your title cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex(
     *     pattern="/[a-zA-Z]+/",
     *     match=true,
     *     message="Title must have only english alphabetical"
     * )
     *
     * @ORM\Column(name="title", type="string", length=25, nullable=false)
     */
    private $title;

    /**
     * @Assert\NotBlank(message="Offer must have category")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="offers")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *
     */
    private $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="date", nullable=false)
     */
    private $dateAdded;

    /**
     * @var string
     * @Assert\NotBlank(message="Offer must have animal type")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnimalCategory", inversedBy="offers")
     * @ORM\JoinColumn(name="animal_category_id", referencedColumnName="id")
     *
     */
    private $animalType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="offers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Animal
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Animal", inversedBy="offers", cascade={"persist"})
     * @ORM\JoinColumn(name="animal_id", referencedColumnName="id", unique=true)
     */
    private $animal;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=10, options={"default" : "open"})
     */
    private $state;

    /**
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="end_point_user", referencedColumnName="id")
     */
    private $endPointUser;

    /**
     * @var float
     *
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     *
     * @ORM\Column(name="price", type="decimal", scale=2, nullable=true)
     */
    private $price;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="offer")
     */
    private $messages;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="offer_bidders",
     *     joinColumns={@ORM\JoinColumn(name="offer_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")})
     */
    private $bidders;

    /**
     * @return ArrayCollection
     */
    public function getBidders()
    {
        return $this->bidders;
    }

    /**
     * @param User $bidder
     * @return Offer
     */
    public function setBidders(User $bidder)
    {
        $this->bidders[] = $bidder;

        return $this;
    }

    /**
     * Offer constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->messages = new ArrayCollection();
        $this->bidders = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }

    /**
     * @param ArrayCollection $messages
     */
    public function setMessages(ArrayCollection $messages): void
    {
        $this->messages = $messages;
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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Offer
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $category
     * @return Offer
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnimalType()
    {
        return $this->animalType;
    }

    /**
     * @param string $animalType
     * @return Offer
     */
    public function setAnimalType($animalType)
    {
        $this->animalType = $animalType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Offer
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Animal
     */
    public function getAnimal()
    {
        return $this->animal;
    }

    /**
     * @param Animal $animal
     * @return Offer
     */
    public function setAnimal($animal)
    {
        $this->animal = $animal;
        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Offer
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndPointUser()
    {
        return $this->endPointUser;
    }

    /**
     * @param mixed $endPointUser
     * @return Offer
     */
    public function setEndPointUser($endPointUser)
    {
        $this->endPointUser = $endPointUser;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Offer
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Offer
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }


}

