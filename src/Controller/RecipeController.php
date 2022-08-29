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

    #[Route("/recettes", name: 'app_recette_index', methods: ['GET'])]
    public function index(DishRepository $repository): Response
    {
        return $this->render("dish/recettes.html.twig", [
            'recettes' => $repository->findAll()
        ]);
    }

    #[Route("/recettes/{id}", name: 'app_recette_show', methods: ['GET'])]
    public function show(Dish $dish): Response
    {
        return $this->render("dish/recette.html.twig", [
            'recette' => $dish
        ]);
    }
}