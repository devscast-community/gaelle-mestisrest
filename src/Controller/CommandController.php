<?php

namespace App\Controller;

use App\Entity\Command;
use App\Form\CommandType;
use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/command')]
class CommandController extends AbstractController
{
    #[Route('/', name: 'app_command_index', methods: ['GET'])]
    public function index(CommandRepository $commandRepository): Response
    {
        return $this->render('command/index.html.twig', [
            'commands' => $commandRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_command_show', methods: ['GET'])]
    public function show(Command $command): Response
    {
        return $this->render('command/show.html.twig', [
            'command' => $command,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_command_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Command $command, CommandRepository $commandRepository): Response
    {
        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandRepository->add($command, true);

            return $this->redirectToRoute('app_command_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('command/edit.html.twig', [
            'command' => $command,
            'form' => $form,
        ]);
    }
}
