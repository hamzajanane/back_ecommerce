<?php

namespace App\Service\Implementation;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceImpl implements UserServiceInterface
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }
    
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
    
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }
    
    public function createUser(string $email, string $password, array $roles = ['ROLE_USER'], string $name = ''): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setName($name);
        $user->setRoles($roles);
        
        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        
        $this->em->persist($user);
        $this->em->flush();
        
        return $user;
    }
    
    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return null;
        }
        
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
        
        $this->em->flush();
        
        return $user;
    }
    
    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            return false;
        }
        
        $this->em->remove($user);
        $this->em->flush();
        
        return true;
    }
    
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}