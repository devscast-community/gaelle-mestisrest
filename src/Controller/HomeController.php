<?php

declare(strict_types=1);

namespace App\Controller;

use App\Data\ContactData;
use App\Form\ContactType;
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
    public function index(): Response
    {
        return $this->render("home.html.twig");
    }

    #[Route("/about", name: "app_about", methods: ["GET"])]
    public function about(): Response
    {
        return $this->render("about.html.twig");
    }

    #[Route("/contact", name: "app_contact", methods: ["GET", "POST"])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from(new Address($data->email, $data->name))
                ->subject($data->subject)
                ->text($data->message)
                ->to(new Address($_ENV['APP_CONTACT_EMAIL'], "Metirest"))
            ;

            try {
                $mailer->send($email);
                $this->addFlash("success", "Nous avons bien reçu votre message !");
            } catch (\Throwable) {
                $this->addFlash("error", "Désolé une erreur est survenue lors du contact !");
            }

            return $this->redirectToRoute("app_index");
        }

        return $this->renderForm(
            view: "contact.html.twig",
            parameters: [
                'form' => $form
            ]
        );
    }
}
