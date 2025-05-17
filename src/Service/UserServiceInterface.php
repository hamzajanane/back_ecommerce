<?php

namespace App\Service;

use App\Entity\User;

interface UserServiceInterface
{
    /**
     * Récupère un utilisateur par son ID
     * 
     * @param int $id L'ID de l'utilisateur
     * @return User|null L'utilisateur trouvé ou null si non trouvé
     */
    public function getUserById(int $id): ?User;
    
    /**
     * Récupère tous les utilisateurs
     * 
     * @return array Tableau de tous les utilisateurs
     */
    public function getAllUsers(): array;
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @param array $roles Rôles de l'utilisateur
     * @param string $name Nom de l'utilisateur
     * @return User L'utilisateur créé
     */
    public function createUser(string $email, string $password, array $roles = ['ROLE_USER'], string $name = ''): User;
    
    /**
     * Met à jour les informations d'un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param array $data Données à mettre à jour
     * @return User|null L'utilisateur mis à jour ou null si non trouvé
     */
    public function updateUser(int $id, array $data): ?User;
    
    /**
     * Supprime un utilisateur
     * 
     * @param int $id ID de l'utilisateur à supprimer
     * @return bool true si la suppression a réussi, false sinon
     */
    public function deleteUser(int $id): bool;
    
    /**
     * Récupère un utilisateur par son email
     * 
     * @param string $email Email de l'utilisateur
     * @return User|null L'utilisateur trouvé ou null si non trouvé
     */
    public function getUserByEmail(string $email): ?User;
}