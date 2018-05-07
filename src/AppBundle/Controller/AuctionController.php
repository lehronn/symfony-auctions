<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AuctionType;

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

			//zapis danych
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($auction); //zapisz obiekt $auction.
			$entityManager->flush(); //zapis do bazy danych.

			return $this->redirectToRoute("auction_index"); //po zapisie danych z formularza przekierowanie na stronę z aukcjami.
		}


		return $this->render("Auction/add.html.twig", ["form" => $form->createView()]);
	}
}
