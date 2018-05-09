<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Auction[]|ArrayCollection //doctrine wymaga by ta zmienna była tylko arraycolections, trzeba to zrobi w konstruktorze
     *
     * @ORM\OneToMany(targetEntity="Auction", mappedBy="owner") //
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $auctions;

    /**
     * @var Offer[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="owner") //jeden user może złoży wiele ofert
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $offers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct(); //w Model\User jest już konstruktor więc tylko go poszerzamy
        $this->auctions = new ArrayCollection();
        $this->offers = new ArrayCollection();
    }

    /**
     * @return Auction[]|ArrayCollection
     */
    public function getAuctions()
    {
        return $this->auctions();
    }

    /**
     * @param Auction $auction
     *
     * @return $this
     */
    public function addAuctions(Auction $auction)
    {
        $$this->auctions[] = $auction;
        return $this;
    }

    /**
    * @return Offer[]|ArrayCollection
    */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param Offer $offer
     *
     * @return $this
     */
    public function addOffer(Offer $offer)
    {
        $this->offers[] = $offer;
        return $this;
    }
}
