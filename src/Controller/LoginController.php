<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface; // Cập nhật import
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // Kiểm tra nếu không có username hoặc password
        if (!$username || !$password) {
            return new JsonResponse(['message' => 'Username and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // Tìm kiếm người dùng trong cơ sở dữ liệu bằng EntityManager
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid credentials');
        }

        // Tạo JWT token cho người dùng đã xác thực thành công
        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }
}
