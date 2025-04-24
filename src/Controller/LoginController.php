<?php

namespace App\Controller;

use OpenApi\Attributes\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class LoginController
{
    public function __construct(){

    }

    public function __invoke():Response{
        return new Response('login');
    }
}