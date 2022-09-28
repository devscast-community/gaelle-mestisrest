<?php

declare(strict_types=1);

namespace App\Controller;

use App\Data\ContactData;
use App\Form\ContactType;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route("/", name: "app_index", methods: ['GET'])]
    public function index(DishRepository $repository): Response
    {
        // utilisation du Dish repository pour récupéré les 4 derniers plats
        // par ordre desc
        $dishes = $repository->findBy([], orderBy: ['id' => 'DESC'], limit: 4);
        return $this->render("home.html.twig", [
            'dishes' => $dishes
        ]);
    }

    #[Route("/about", name: "app_about", methods: ["GET"])]
    public function about(): Response
    {
        // affichage de la page
        return $this->render("about.html.twig");
    }

    #[Route("/contact", name: "app_contact", methods: ["GET", "POST"])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactData(); // création de données de contact - non rempli
        $form = $this->createForm(ContactType::class, $data)
            ->handleRequest($request); // une fois le formulaire soumis les données seront rempli dans l'objet $data

        // vérification de la validité
        if ($form->isSubmitted() && $form->isValid()) {

            // création de l'email que l'on envoyé
            $email = (new Email())
                ->from(new Address($data->email, $data->name)) // de qui ?
                ->subject($data->subject) // le sujet
                ->text($data->message) // contenu
                ->to(new Address($_ENV['APP_CONTACT_EMAIL'], "Metirest"));  // à qui ?

            // si jamais on a une exception on l'a capture et on envoie le message à l'utilisateur
            try {
                $mailer->send($email);
                $this->addFlash("success", "Nous avons bien reçu votre message !");
            } catch (\Throwable) {
                $this->addFlash("error", "Désolé une erreur est survenue lors du contact !");
            }

            // redirection vers la page d'accueil
            return $this->redirectToRoute("app_index");
        }

        // affichage de la page et du formulaire
        return $this->renderForm(
            view: "contact.html.twig",
            parameters: [
                'form' => $form
            ]
        );
    }
}
