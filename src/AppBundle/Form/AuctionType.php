<?php

namespace AppBundle\Form;

use AppBundle\Entity\Auction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//klasa tworząca formularz dodawania aukcji.
class AuctionType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add("title", TextType::class, ["label"=>"Tytuł"])
			->add("description", TextareaType::class, ["label"=>"Opis"])
			->add("price", TextType::class, ["label"=>"Cena"])
			->add("submit", SubmitType::class, ["label"=>"Dodaj"]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(["data_class" => Auction::class]); //definiujemy domyśly rodzaj danych, należy dane wstawić do obiektu $Auction w encji Auction
	}
}
