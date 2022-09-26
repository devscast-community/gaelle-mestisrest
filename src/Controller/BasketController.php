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
        $basket = $this->getBasket($request); // récupère le panier de l'utilisateur
        $index = $this->getItemIndex($basket, $dish); // récupère la position du plat dans le panier

        // si la position est NULL alors on ajoute le plat
        // sinon on augmente la quantité
        if (!is_null($index)) {
            $basket[$index]['quantity'] = $basket[$index]['quantity'] + 1; // incrémentation de la quantité
        } else {
            $basket[] = ['dish' => $dish, 'quantity' => 1]; // ajout dans le panier, le panier est un tableau PHP
        }

        $request->getSession()->set('_basket', $basket); // sauvegarde dans la session
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
        $basket = $this->getBasket($request); // récupère le panier de l'utilisateur
        $basket = array_filter($basket, function (array $d) use ($dish) {
            return $d['dish']->getId() !== $dish->getId(); // on garde tout ce qui est différent de l'id qu'on veut supprimer
        });

        $request->getSession()->set('_basket', $basket); // sauvegarde du panier en session
        return $this->redirectToRoute('app_basket_index');
    }

    #[Route('/validate', name: 'app_basket_validate', methods: ['POST'])]
    public function validate(
        Request $request,
        CommandRepository $repository,
        DishRepository $dishRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser(); // on récupère l'utilisateur connecté car c'est lui qui passe la commande
        $basket = $this->getBasket($request); // on récupère le pannier en session
        $quantity = 0;

        // on parcours tous les éléments du panier en sommant la quantité
        foreach ($basket as $item) {
            $quantity += $item['quantity'];
        }

        // on passe commande seulement si le pannier n'est pas vide
        if ($quantity > 0) {
            $command = (new Command())
                ->setOwner($user) // précise l'utilisateur qui passe la commande
                ->setStatus('pending') // le status "en attente" par défaut
                ->setCreatedAt(new \DateTimeImmutable()); // la date actuelle

            $prixTotal = 0;
            foreach($basket as $item) {
                $command->addDish($dishRepository->find($item['dish']->getId())); // on rajoute les plats dans la commande
                $prixTotal += ($item['dish']->getPrice() * $item['quantity']); // on multiplie par la quantité pour avoir le prix
            }

            $command->setTotalPrice($prixTotal); // on affecte le prix total du pannier à la commande
            $repository->add($command, true); // on sauvegarde dans la bdd
            $request->getSession()->set('_basket', []); // on vide le pannier
        }

        return $this->redirectToRoute('app_profile_command_index');
    }

    private function getBasket(Request $request): array
    {
        // on utilise la session de l'utilisateur pour sauvegarder le panier
        return $request->getSession()->get('_basket', []);
    }
}
