<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessagesRepository")
 */
class Message
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
     * @ORM\Column(name="content", type="text", length=255)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var Offer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Offer", inversedBy="messages")
     */
    private $offer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="messages")
     */
    private $startUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="date", nullable=false)
     */
    private $dateAdded;

    /**
     * Offer constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
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
     * Set content
     *
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getStartUser()
    {
        return $this->startUser;
    }

    /**
     * @param User $startUser
     * @return Message
     */
    public function setStartUser($startUser)
    {
        $this->startUser = $startUser;

        return $this;
    }

    /**
     * @return Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param Offer $offer
     * @return Message
     */
    public function setOffer(Offer $offer)
    {
        $this->offer = $offer;

        return $this;
    }
}

