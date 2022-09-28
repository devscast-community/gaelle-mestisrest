<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); // un object non rempli
        $form = $this->createForm(RegistrationFormType::class, $user)
            ->handleRequest($request);

        // on passe en get la variable admin pour pouvoir créer un administrateur
        $isAdmin = $request->query->getInt('admin', 0);

        if ($form->isSubmitted() && $form->isValid()) {

            // attribution du role admin si jamais la variable admin == 1
            $user->setRoles([$isAdmin === 1 ? "ROLE_ADMIN" : "ROLE_USER"]);

            // Hashage du mot de passe pour plus de sécurité
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // enregistrement dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // affichage de la vue
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
