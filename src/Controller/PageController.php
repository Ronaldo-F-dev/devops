<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em,private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): JsonResponse
    {
        return $this->json([
            'success' => true,
            'status' => 200,
        ]);
    }

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        
        $userExist = $this->userRepository->findOneBy(['email' => $email, 'password' => $password]);
        if(!$userExist) {
            return $this->json([
                'success' => false,
                'status' => 401,
                'message' => 'Invalid credentials',
            ]);
        }
        return $this->json([
            'success' => true,
            'status' => 200,
            'message' => 'Login successful',
            'user' => [
                "id" => $userExist->getId(),
                "email" => $userExist->getEmail(),
                "nom" => $userExist->getNom(),
                "createdAt" => $userExist->getCreatedAt(),
                "updatedAt" => $userExist->getUpdatedAt(),
            ]
        ]);
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $email = $request->request->get('email') ?? null;
        $password = $request->request->get('password') ?? null;
        $nom = $request->request->get('nom') ?? null;
        if(!$email || !$password || !$nom) {
            return $this->json([
                'success' => false,
                'status' => 400,
                'message' => 'Missing required fields',
            ]);
        }
        $userExist = $this->userRepository->findOneBy(['email' => $email]);
        if($userExist) {
            return $this->json([
                'success' => false,
                'status' => 400,
                'message' => 'Email already exists',
            ]);
        }
            $user = new User();
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setNom($nom);
            $this->em->persist($user);
            $this->em->flush();
            return $this->json([
                'success' => true,
                'status' => 201,
                'message' => 'User created successfully',
                'user' => [
                    "id" => $user->getId(),
                    "email" => $user->getEmail(),
                    "nom" => $user->getNom(),
                    "createdAt" => $user->getCreatedAt(),
                    "updatedAt" => $user->getUpdatedAt(),
                ]
            ]);
        
    }


    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(Request $request): JsonResponse
    {
        $id = $request->query->get('id');
        $userExist = $this->userRepository->findOneBy(['id' => $id]);
        if(!$userExist) {
            return $this->json([
                'success' => false,
                'status' => 404,
                'message' => 'User not found',
            ]);
        }
        return $this->json([
            'success' => true,
            'status' => 200,
            'message' => 'User profile',
            'user' => [
                "id" => $userExist->getId(),
                "email" => $userExist->getEmail(),
                "nom" => $userExist->getNom(),
                "createdAt" => $userExist->getCreatedAt(),
                "updatedAt" => $userExist->getUpdatedAt(),
            ]
        ]);
    }
}