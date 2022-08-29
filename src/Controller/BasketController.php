<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Dish;
use App\Entity\User;
use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profile/basket")]
final class BasketController extends AbstractController
{
    #[Route("", name: 'app_basket_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $basket = $this->getBasket($request);
        return $this->render('basket.html.twig', [
           'basket' => $basket
        ]);
    }

    #[Route("/add/{id}", name: 'app_basket_add', methods: ['POST'])]
    public function add(Request $request, Dish $dish): Response
    {
        $basket = $this->getBasket($request);
        $basket[] = $dish;
        $request->getSession()->set('_panier', $basket);
        return $this->redirectToRoute('app_recette_index');
    }

    #[Route("/remove/{id}", name: 'app_basket_remove', methods: ['POST'])]
    public function remove(Request $request, Dish $dish): Response
    {
        $basket = $this->getBasket($request);
        $basket = array_filter($basket, function (Dish $d) use ($dish) {
            return $d->getId() !== $dish->getId();
        });
        $request->getSession()->set('_panier', $basket);
        return $this->redirectToRoute('app_recette_index');
    }

    public function validate(Request $request, CommandRepository $repository,): Response {
        /** @var User $user */
        $user = $this->getUser();
        $basket = $this->getBasket($request);
        $quantity = count($basket);

        if ($quantity > 0) {
            $command = (new Command())
                ->setOwner($user)
                ->setStatus('pending')
                ->setCreatedAt(new \DateTimeImmutable());

            $prixTotal = 0;
            /** @var Dish $dish */
            foreach($basket as $dish) {
                $prixTotal += $dish->getPrice();
                $command->addDish($dish);
            }

            $command->setTotalPrice($prixTotal);
            $repository->add($command, true);
            $request->getSession()->set('_panier', []);
        }

        return $this->redirectToRoute('app_index');
    }

    private function getBasket(Request $request): array
    {
        return $request->getSession()->get('_panier', []);
    }
}
