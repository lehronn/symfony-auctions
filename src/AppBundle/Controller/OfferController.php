<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Offer;
use AppBundle\Entity\Auction;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\BidType;

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

        $this->addFlash("success", "You buy auction: {$auction->getTitle()}. Price: {$offer->getPrice()}zł.");

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }

    /**
    * @Route("/auction/bid/{id}", name="offer_bid", methods={"POST"})
    *
    * @param Request $request
    * @param Auction $auction
    *
    * @return \Symfony\Componenet\HttpFoundation\RedirectResponse
    */
    public function bidAction(Request $request, Auction $auction)
    {
        $offer = new Offer();
        $bidForm = $this->createForm(BidType::class, $offer);

        $bidForm->handleRequest($request);

        if ($bidForm->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $lastOffer = $entityManager
                ->getRepository(Offer::class)
                ->findOneBy(["auction" => $auction], ["createdAt" => "DESC"]); // pole po którym szukamy => pole po którym sortujemy i kierunek sortowania w wartości

            if (isset($lastOffer)) {//jeśli istnieje poprzednia oferta to sprawdzamy czy nie jest większa lub równa od tego co user chce zalicytowac.
                if ($offer->getPrice() <= $lastOffer->getPrice())
                {
                    $this->addFlash("error", "Your offer should be greater than the previous highest offer.");
                    return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
                }
            } else {
                if ($offer->getPrice() < $auction->getStartingPrice()) {
                    $this->addFlash("error", "Your offer should be greater than the starting price of auction.");

                    return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
                }
            }

            $offer
            ->setType(Offer::TYPE_BID)
            ->setAuction($auction);

            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash("success", "You bid auction: {$auction->getTitle()}. Your offer is: {$offer->getPrice()}zł.");
        } else {
            $this->addFlash("error", "Biding is aborted. Auction: {$auction->getTitle()}.");
        }

        return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    }
}
