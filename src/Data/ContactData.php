<?php

declare(strict_types=1);

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

// ceci est un DTO c'est à dire Data Transfert Object
// son role est de transférer des informations d'un A à un B dans l'application
final class ContactData
{
    #[Assert\NotBlank] // validation des données
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $subject = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $message =  null;
}
