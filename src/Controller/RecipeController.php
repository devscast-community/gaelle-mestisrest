<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RecipeController extends AbstractController
{

    #[Route("/dishes", name: 'app_recette_index', methods: ['GET'])]
    public function index(DishRepository $repository): Response
    {
        return $this->render("dish/recettes.html.twig", [
            'dishes' => $repository->findAll() // findAll permet de récupérer toutes les informations
        ]);
    }

    #[Route("/dishes/{id}", name: 'app_recette_show', methods: ['GET'])]
    public function show(Dish $dish): Response
    {
        // Symfony va faire une correspondence entre {id} et le type Dish (@paramConverter)
        // si il existe une entity Dish avec un id précisé dans l'URL on affiche les informations
        // sinon on a une erreur 404

        return $this->render("dish/recette.html.twig", [
            'dish' => $dish
        ]);
    }
}