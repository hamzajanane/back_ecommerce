<?php

namespace App\Controller;

use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/users')]
class UserController extends AbstractController
{
    private UserServiceInterface $userService;
    private SerializerInterface $serializer;
    
    public function __construct(
        UserServiceInterface $userService,
        SerializerInterface $serializer
    ) {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'get_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            $data = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }
            $data = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
            return new JsonResponse($data, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la récupération de l\'utilisateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
                return new JsonResponse(['message' => 'Email, mot de passe et nom sont requis'], Response::HTTP_BAD_REQUEST);
            }

            
            $roles = isset($data['roles']) && is_array($data['roles']) ? $data['roles'] : ['ROLE_USER'];

            
            $existingUser = $this->userService->getUserByEmail($data['email']);
            if ($existingUser) {
                return new JsonResponse(['message' => 'Cet email est déjà utilisé'], Response::HTTP_CONFLICT);
            }

            
            $user = $this->userService->createUser($data['email'], $data['password'], $roles, $data['name']);
            $userData = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
            
            return new JsonResponse($userData, Response::HTTP_CREATED, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
           
            $existingUser = $this->userService->getUserById($id);
            if (!$existingUser) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }
            
            
            if (isset($data['email']) && $data['email'] !== $existingUser->getEmail()) {
                $userWithEmail = $this->userService->getUserByEmail($data['email']);
                if ($userWithEmail && $userWithEmail->getId() !== $id) {
                    return new JsonResponse(['message' => 'Cet email est déjà utilisé par un autre utilisateur'], Response::HTTP_CONFLICT);
                }
            }
            
            $user = $this->userService->updateUser($id, $data);
            $userData = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
            return new JsonResponse($userData, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $result = $this->userService->deleteUser($id);
            
            if (!$result) {
                return new JsonResponse(['message' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}