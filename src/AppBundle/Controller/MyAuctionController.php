<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Auction;
use AppBundle\Form\BidType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\AuctionType;
use Symfony\Component\Form\FormError;

class MyAuctionController extends Controller
{
    /**
    * @Route("/my", name="my_auction_index")
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function indexAction() {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $auctions = $entityManager
            ->getRepository(Auction::class)
            ->findBy(["owner" => $this->getUser()]);

            return $this->render("MyAuction/index.html.twig", ["auctions" => $auctions]);
    }

    /**
    * @Route("/my/auction/details/{id}", name="my_auction_details")
    *
    * @param Auction $auction
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function detailsAction(Auction $auction)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        if ($auction->getStatus() === Auction::STATUS_FINISHED)
        {
            return $this->render("MyAuction/finished.html.twig", ["auction" => $auction]);
        }

        $deleteForm = $this->createFormBuilder()
		->setAction($this->generateUrl("my_auction_delete", ["id" => $auction->getId()]))
		->setMethod(Request::METHOD_DELETE)
		->add("submit", SubmitType::class, ["label" => "Delete"])
		->getForm();

		$finishForm = $this->createFormBuilder()
		->setAction($this->generateUrl("my_auction_finish", ["id" => $auction->getId()]))
		->add("submit", SubmitType::class, ["label" => "Finish"])
		->getForm();

        return $this->render(
            "MyAuction/details.html.twig",
            [
                "auction" => $auction,
				"deleteForm" => $deleteForm->createView(),
				"finishForm" => $finishForm->createView(),
        ]
        );
    }

    /**
    * @Route("/my/auction/add", name="my_auction_add")
    *
    * @param Request $request
    *
    * @return Response
    */
    public function addAction(Request $request) //request zawiera wszystko co przychodzi z metody POST.
    {
        $this->denyAccessUnlessGranted("ROLE_USER"); //przekierowuje na formularz logowania jeżeli nie jesteś zalogowany

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
                $auction
                    ->setStatus(Auction::STATUS_ACTIVE)
                    ->setOwner($this->getUser()); //ustawia właściciela aukcji na pobranego getUser() czyli tego usera któy jest teraz zalogowany.

                //zapis danych
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction); //zapisz obiekt $auction.
                $entityManager->flush(); //zapis do bazy danych.

                $this->addFlash("success", "Auction {$auction->getTitle()} is added.");

                return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]); //po zapisie danych z formularza przekierowanie na stronę ze szczegółami aukcji po ID.
            }

            $this->addFlash("error", "Auction {$auction->getTitle()} isn`t added! Error.");
        }

        return $this->render("MyAuction/add.html.twig", ["form" => $form->createView()]);
    }

    	/**
    	* @Route("/my/auction/edit/{id}", name="my_auction_edit")
    	*
    	* @param Request $request
        *
    	* @param Auction $auction
    	*
    	* @return Response
    	*/
    	public function editAction(Request $request, Auction $auction)
    	{
    		$this->denyAccessUnlessGranted("ROLE_USER");

    		if ($this->getUser() !== $auction->getOwner()) //jeśli nie jesteś właścicielem aukcji to jej nie zedytujesz.
    		{
    			throw new AccessDeniedException();
    		}

    		$form= $this->createForm(AuctionType::class, $auction);

    		if($request->isMethod("post")) {
    			$form->handleRequest($request);

    			$entityManager = $this->getDoctrine()->getManager();
    			// $entityManager = persist($auction); z tą linijką nie działa.
    			$entityManager->flush();

    			$this->addFlash("success", "Auction {$auction->getTitle()} is edited.");

    			return $this->redirectToRoute("auction_details", ["id" => $auction->getId()]);
    		}

    		return $this->render("MyAuction/edit.html.twig", ["form" => $form->createView()]);
    	}

        	/**
        	* @Route("/my/auction/delete/{id}", name="my_auction_delete", methods={"DELETE"})
        	*
        	* @param Auction $auction
        	*
        	* @return RedirectResponse
        	*/
        	public function deleteAction(Auction $auction)
        	{
        		$this->denyAccessUnlessGranted("ROLE_USER");

        		if ($this->getUser() !== $auction->getOwner()) //jeśli nie jesteś właścicielem aukcji to jej nie usuniesz.
        		{
        			throw new AccessDeniedException();
        		}

        		$entityManager = $this->getDoctrine()->getManager();
        		$entityManager->remove($auction);
        		$entityManager->flush();

        		$this->addFlash("success", "Auction {$auction->getTitle()} is deleted.");

        		return $this->redirectToRoute("my_auction_index");
        	}

            /**
            * @Route("/my/auction/finish/{id}", name="my_auction_finish", methods={"POST"})
            *
            * @param Auction $auction
            *
            * @return \Symfony\Componenet\HttpFoundation\RedirectResponse
            */
            public function finishAction(Auction $auction)
            {
                $this->denyAccessUnlessGranted("ROLE_USER");

                if ($this->getUser() !== $auction->getOwner()) //jeśli nie jesteś właścicielem aukcji to jej nie zakończysz.
                {
                    throw new AccessDeniedException();
                }

                $auction
                    ->setExpiresAt(new \DateTime())
                    ->setStatus(Auction::STATUS_FINISHED);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auction);
                $entityManager->flush();

                $this->addFlash("success", "Auction {$auction->getTitle()} is finished.");

                return $this->redirectToRoute("my_auction_details", ["id" => $auction->getId()]);
            }
}
