<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Animal
 *
 * @ORM\Table(name="animal")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnimalRepository")
 */
class Animal
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
     * @ORM\Column(name="name", type="string", length=15, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Animal must have breed")
     *
     * @ORM\Column(name="breed", type="string", length=25, nullable=true)
     */
    private $breed;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Animal must have description")
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var int
     *
     * @Assert\NotBlank(message="Animal must have age even if its 0")
     *
     * @ORM\Column(name="age", type="integer", nullable=false)
     */
    private $age;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Animal must have picture")
     *
     * @ORM\Column(name="picture", type="string", length=255, nullable=false)
     */
    private $picture;

    /**
     * @var ArrayCollection|Offer[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Offer", mappedBy="animal")
     */
    private $offers;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Animal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set breed
     *
     * @param string $breed
     *
     * @return Animal
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;

        return $this;
    }

    /**
     * Get breed
     *
     * @return string
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Animal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set age
     *
     * @param integer $age
     *
     * @return Animal
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
     * Set picture
     *
     * @param string $picture
     *
     * @return Animal
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }
}

