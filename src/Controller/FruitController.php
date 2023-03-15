<?php

namespace App\Controller;

use App\Entity\Fruit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Knp\Component\Pager\PaginatorInterface;

class FruitController extends AbstractController
{

	public EntityManagerInterface $em;
	public PaginatorInterface $paginator;


	public function __construct( EntityManagerInterface $em ,PaginatorInterface $paginator) {

		$this->em = $em;
		$this->paginator = $paginator;

	}

    #[Route('/fruit', name: 'app_fruit')]
    public function index(Request $request): Response
    {


		$fruits = $this->em->getRepository(Fruit::class)->findAll();
		$fruits = $this->paginator->paginate($fruits,$request->query->getInt('page',1),$request->query->getInt('limit',10));

        return $this->render('fruit/index.html.twig', [
            'fruits' => $fruits,
        ]);
    }
	public function addToFavorite(Request $request):JsonResponse{

		$id = json_decode($request->getContent())->id;
		$favorite = $this->em->getRepository(Fruit::class)->find($id);
		if( $favorite->getIsFavorite())
		{
			$favorite->setIsFavorite(0);
		}
		else{
			$favorite->setIsFavorite(1);
		}

		$this->em->persist($favorite);
		$this->em->flush();
		return $this->json('helo');
	}
	public function getFavorites(){
		$favorites = $this->em->getRepository(Fruit::class)->findByFavorite(true);
		$total = ["carbohydrates"=>0,"protein"=>0,"fat"=>0,"calories"=>0,"sugar"=>0];
		foreach($favorites as $favorite)
		{
			$nutritions = $favorite->getNutritions();
			$total["carbohydrates"] +=$nutritions["carbohydrates"];
			$total["protein"] +=$nutritions["protein"];
			$total["fat"] +=$nutritions["fat"];
			$total["calories"] +=$nutritions["calories"];
			$total["sugar"] +=$nutritions["sugar"];

		}

		return $this->render('fruit/favorites.html.twig', [
			'favorites' => $favorites,'total'=>$total
		]);
	}

}
