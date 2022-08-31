<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Command;
use App\Entity\User;
use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/profile/command")]
final class UserCommandController extends AbstractController
{
    #[Route("", name: "app_profile_command_index", methods: ['GET'])]
    public function index(CommandRepository $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $commands = $repository->findBy(['owner' => $user]);

        return $this->render("command/user.html.twig", [
            'commands' => $commands
        ]);
    }

    #[Route('/{id}', name: 'app_profile_command_delete', methods: ['POST'])]
    public function delete(Request $request, Command $command, CommandRepository $commandRepository): Response
    {
        if ($command->getStatus() === "pending") {
            if ($this->isCsrfTokenValid('delete'.$command->getId(), $request->request->get('_token'))) {
                $commandRepository->remove($command, true);
            }
        }

        return $this->redirectToRoute('app_profile_command_index', [], Response::HTTP_SEE_OTHER);
    }
}
