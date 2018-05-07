<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AuctionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AuctionController extends Controller
{
	/**
	 * @Route("/", name="auction_index")
	 *
	 * @return Response
	 */
	public function indexAction()
	{
		$entityManager = $this->getDoctrine()->getManager();
		$auctions = $entityManager->getRepository(Auction::class)->findAll(); //pobiera wszystkie dane z bazy danych sqlite przez Doctrine.

		return $this->render("Auction/index.html.twig", ["auctions" => $auctions]);
	}

	/**
	* @Route("/{id}", name="auction_details")
	*
	* @param Auction $auction
	*
	* @return Response
	*/
	public function detailsAction(Auction $auction)
	{
		return $this->render("Auction/details.html.twig", ["auction" => $auction]);
	}

	/**
	* @Route("/auction/add", name="auction_add")
	*
	* @return Response
	*/
	public function addAction(Request $request) //request zawiera wszystko co przychodzi z metody POST.
	{
		$auction = new Auction(); //nowy obiekt aukcji.

		$form = $this->createForm(AuctionType::class, $auction); //tworzymy formularz typu AuctionType z AppBundle/Form i używamy encji $auction

		if($request->isMethod("post"))
		{
			$form->handleRequest($request); //to co przyszło przez $request z POST zostaje wstawione do formularza

			$auction->setStatus(Auction::STATUS_ACTIVE);

			//zapis danych
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($auction); //zapisz obiekt $auction.
			$entityManager->flush(); //zapis do bazy danych.

			return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]); //po zapisie danych z formularza przekierowanie na stronę ze szczegółami aukcji po ID.
		}


		return $this->render("Auction/add.html.twig", ["form" => $form->createView()]);
	}

	/**
	* @Route("/auction/edit/{id}", name="auction_edit")
	*
	* @param Request $request
	* @param Auction $auction
	*
	* @return Response
	*/
	public function editAction(Request $request, Auction $auction)
	{
		$form= $this->createForm(AuctionType::class, $auction);

		if($request->isMethod("post")) {
			$form->handleRequest($request);

			$entityManager = $this->getDoctrine()->getManager();
			// $entityManager = persist($auction); z tą linijką nie działa.
			$entityManager->flush();

			return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
		}

		return $this->render("Auction/edit.html.twig", ["form" => $form->createView()]);
	}
}
