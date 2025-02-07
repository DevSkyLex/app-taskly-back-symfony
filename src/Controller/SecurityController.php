<?php

namespace App\Controller;

use App\DTO\UserRegisterDTO;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/api/security', name: 'security_')]
final class SecurityController extends AbstractController
{
  #[Route(path: '/login', name: 'login', methods: ['POST'])]
  public function login(): JsonResponse 
  {
    return $this->json(data: [
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/SecurityController.php',
    ]);
  }
}
