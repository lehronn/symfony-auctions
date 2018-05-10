<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use AppBundle\Entity\User;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
				"buyForm" => $buyForm->createView(),
				"bidForm" => $bidForm->createView(),
		]
		);
	}
}
