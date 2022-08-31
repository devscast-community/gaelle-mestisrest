<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Dish;
use App\Entity\User;
use App\Repository\CommandRepository;
use App\Repository\DishRepository;
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
        $index = $this->getItemIndex($basket, $dish);

        if (!is_null($index)) {
            $basket[$index]['quantity'] = $basket[$index]['quantity'] + 1;
        } else {
            $basket[] = ['dish' => $dish, 'quantity' => 1];
        }

        $request->getSession()->set('_basket', $basket);
        return $this->redirectToRoute('app_recette_index');
    }

    private function getItemIndex(array $basket, Dish $dish): ?int
    {
        /**
         * @var int $key
         * @var array<string, Dish|int> $value
         */
        foreach ($basket as $key => $value) {
            if ($value['dish']->getId() === $dish->getId()) {
                return $key;
            }
        }
        return null;
    }

    #[Route("/remove/{id}", name: 'app_basket_remove', methods: ['POST'])]
    public function remove(Request $request, Dish $dish): Response
    {
        $basket = $this->getBasket($request);
        $basket = array_filter($basket, function (array $d) use ($dish) {
            return $d['dish']->getId() !== $dish->getId();
        });

        $request->getSession()->set('_basket', $basket);
        return $this->redirectToRoute('app_basket_index');
    }

    #[Route('/validate', name: 'app_basket_validate', methods: ['POST'])]
    public function validate(
        Request $request,
        CommandRepository $repository,
        DishRepository $dishRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $basket = $this->getBasket($request);
        $quantity = 0;
        foreach ($basket as $item) {
            $quantity += $item['quantity'];
        }

        if ($quantity > 0) {
            $command = (new Command())
                ->setOwner($user)
                ->setStatus('pending')
                ->setCreatedAt(new \DateTimeImmutable());

            $prixTotal = 0;
            foreach($basket as $item) {
                $command->addDish($dishRepository->find($item['dish']->getId()));
                $prixTotal += ($item['dish']->getPrice() * $item['quantity']);
            }

            $command->setTotalPrice($prixTotal);
            $repository->add($command, true);
            $request->getSession()->set('_basket', []);
        }

        return $this->redirectToRoute('app_profile_command_index');
    }

    private function getBasket(Request $request): array
    {
        return $request->getSession()->get('_basket', []);
    }
}
