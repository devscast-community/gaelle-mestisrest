<?php

declare(strict_types=1);

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactData
{
    #[Assert\NotBlank]
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
