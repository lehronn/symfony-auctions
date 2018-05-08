<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Auction;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends Controller
{
    /**
    * @Route("/auction/buy/{id}", name="offer_buy", methods={"POST"})
    *
    * @param Auction $auction
    *
    * @return \Symfony\Componenet\HttpFoundation\RedirectResponse
    */
    public function buyAction(Auction $auction)
    {
        $offer = new Offer(); //tworzysz nową ofertę
        $offer
            ->setAuction($auction)
            ->setType(Offer::TYPE_BUY) //ustaw typ nowej oferty na buy z encji Entity/Offer.php
            ->setPrice($auction->getPrice());

        $auction
            ->setStatus(Auction::STATUS_FINISHED)
            ->setExpiresAt(new \DateTime());

        $entityManager =  $this->getDoctrine()->getManager();
        $entityManager->persist($auction);
        $entityManager->persist($offer);
        $entityManager->flush();

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}
