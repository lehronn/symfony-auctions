<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Form\AuctionType;
use AppBundle\Form\BidType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use AppBundle\Service\DateService;

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
		$auctions = $entityManager->getRepository(Auction::class)->findBy(["status" => Auction::STATUS_ACTIVE]); //pobieraaktywne aukcje z bazy danych sqlite przez Doctrine.

		return $this->render("Auction/index.html.twig", ["auctions" => $auctions]);
	}

	/**
	* @Route("/auction/details/{id}", name="auction_details")
	*
	* @param Auction $auction
	*
	* @return Response
	*/
	public function detailsAction(Auction $auction)
	{
		if ($auction->getStatus() === Auction::STATUS_FINISHED)
		{
			return $this->render("Auction/finished.html.twig", ["auction" => $auction]);
		}

		$deleteForm = $this->createFormBuilder()
		->setAction($this->generateUrl("auction_delete", ["id" => $auction->getId()]))
		->setMethod(Request::METHOD_DELETE)
		->add("submit", SubmitType::class, ["label" => "Delete"])
		->getForm();

		$finishForm = $this->createFormBuilder()
		->setAction($this->generateUrl("auction_finish", ["id" => $auction->getId()]))
		->add("submit", SubmitType::class, ["label" => "Finish"])
		->getForm();

		$buyForm = $this->createFormBuilder()
		->setAction($this->generateUrl("offer_buy", ["id" => $auction->getId()]))
		->add("submit", SubmitType::class, ["label" => "Buy"])
		->getForm();

		$bidForm = $this->createForm
		(
            BidType::class,
            null,
            ["action" => $this->generateUrl("offer_bid", ["id" => $auction->getId()])]
		);

		return $this->render(
			"Auction/details.html.twig",
			[
				"auction" => $auction,
				"deleteForm" => $deleteForm->createView(),
				"finishForm" => $finishForm->createView(),
				"buyForm" => $buyForm->createView(),
				"bidForm" => $bidForm->createView(),
		]
		);
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

			if($auction->getStartingPrice() >= $auction->getPrice())
			{
				$form->get("startingPrice") //pobieramy pole startingPrice w formularzu.
					->addError(new FormError("Price shoud be greater than starting price.")); //i dodajemy do pola error
			}

			if($form->isValid())
			{
				$auction->setStatus(Auction::STATUS_ACTIVE);

				//zapis danych
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($auction); //zapisz obiekt $auction.
				$entityManager->flush(); //zapis do bazy danych.

				$this->addFlash("success", "Auction {$auction->getTitle()} is added.");

				return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]); //po zapisie danych z formularza przekierowanie na stronę ze szczegółami aukcji po ID.
			}

			$this->addFlash("error", "Auction {$auction->getTitle()} isn`t added! Error.");
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

			$this->addFlash("success", "Auction {$auction->getTitle()} is edited.");

			return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
		}

		return $this->render("Auction/edit.html.twig", ["form" => $form->createView()]);
	}

	/**
	* @Route("/auction/delete/{id}", name="auction_delete", methods={"DELETE"})
	*
	* @param Auction $auction
	*
	* @return \Symfony\Componenet\HttpFoundation\RedirectResponse
	*/
	public function deleteAction(Auction $auction)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($auction);
		$entityManager->flush();

		$this->addFlash("success", "Auction {$auction->getTitle()} is deleted.");

		return $this->redirectToRoute("auction_index");
	}

	/**
	* @Route("/auction/finish/{id}", name="auction_finish", methods={"POST"})
	*
	* @param Auction $auction
	*
	* @return \Symfony\Componenet\HttpFoundation\RedirectResponse
	*/
	public function finishAction(Auction $auction)
	{
		$auction
			->setExpiresAt(new \DateTime())
			->setStatus(Auction::STATUS_FINISHED);

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($auction);
		$entityManager->flush();

		$this->addFlash("success", "Auction {$auction->getTitle()} is finished.");

		return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
	}
}
