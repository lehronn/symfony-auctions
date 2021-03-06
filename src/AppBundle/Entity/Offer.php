<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Offer
 *
 * @ORM\Table(name="offer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OfferRepository")
 */
class Offer
{
    const TYPE_BUY = "buy";
    const TYPE_BID = "bid";
    const TYPE_AUCTION = "auction";

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
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(message="Price is required.")
     * @Assert\GreaterThan(value=0, message="Price should greater than 0.")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=16)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updateAt;

    /**
     * @var Auction
     * pole $auction będzie odpowiada Entity/Auction.php
     * w encji Auction dodamy pole offers żeby można łatwo pobierac oferty danej aukcji
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="offers")
     *
     * w tabeli offer będzie auction_id i będzie ono odpowiadac polu id w encji Auction.
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    private $auction;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="offers")
     */
    private $owner;

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
     * Set price
     *
     * @param string $price
     *
     * @return Offer
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Offer
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Offer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Offer
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * @param Auction $auction
     *
     * @return $this
     */
    public function setAuction(Auction $auction)
    {
        $this->auction = $auction;
        return $this;
    }

    /**
     * @return Auction
     */
    public function getAuction(Auction $auction)
    {
        return $this->auction;
    }

    /**
     * @param Offer $offer
     * @return $this
     */
    public function addOffer(Offer $offer)
    {
        $this->offers[] = $offer;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
