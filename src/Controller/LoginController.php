<?php

namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;  // Thêm import Firebase JWT
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private string $secretKey; // Khóa bí mật dùng để mã hóa token

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->secretKey = 'your_secret_key'; // Thay bằng khóa bí mật của bạn
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
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // Token có hiệu lực trong 1 giờ
        $payload = [
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "data" => [
                "username" => $user->getUsername(),
                "roles" => $user->getRoles(),
            ]
        ];

        $token = JWT::encode($payload, $this->secretKey, 'HS256'); // Thêm tham số thuật toán

        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }
}
