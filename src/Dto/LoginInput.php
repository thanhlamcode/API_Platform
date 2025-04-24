<?php

// src/Dto/LoginInput.php
namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;

class LoginInput
{
    #[Assert\NotBlank]
    public string $username;

    #[Assert\NotBlank]
    public string $password;
}
