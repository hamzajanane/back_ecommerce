# ECommerce - Plateforme E-commerce avec Symfony & React

## Description


Ce projet est une application web e-commerce développée avec Symfony (backend) et React (frontend).
Il permet aux utilisateurs de consulter un catalogue de produits, de gérer un panier, de passer commande et de payer en ligne via Stripe(test). 
Un back-office est également disponible pour les administrateurs afin de gérer les produits, les catégories et consulter les commandes.

### Fonctionnalités principales

- **Côté client :**
  - Catalogue de produits avec filtres et recherche
  - Système de panier d'achat
  - Processus de commande fluide
  - Paiement sécurisé via Stripe
  - Suivi des commandes

- **Back-office administrateur :**
  - Gestion des produits et catégories
  - Suivi et traitement des commandes
  - Tableaux de bord et statistiques
  -Systéme de notification par mail après commande 

##  Technologies utilisées

### Backend
- **Symfony 6.x**
- **Doctrine ORM**
- **Symfony Mailer**

### Frontend
- **React 18**
- **Axios**
- **Styled Components/Tailwind CSS**

### Base de données & Services
- **PostgreSQL/MySQL**
- **Stripe API** (paiement en ligne)

##  Installation & Configuration

### Prérequis

- PHP >= 8.1
- Composer
- Node.js >= 16.x et npm
- PostgreSQL/MySQL
- Git
- Compte Stripe (clés API de test)

### Configuration du Backend (Symfony)

1. **Cloner le dépôt :**
git clone https://github.com/hamzajanane/back_ecommerce.git
cd back_ecommerce

2. **Installer les dépendances :**
composer install

3. **Configuration de l'environnement :**
   - Copier le fichier `.env` en `.env.local`
   - Configurer les variables d'environnement :
```
# Configuration de la base de données
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/ecommerce_db"


# Configuration Stripe
STRIPE_SECRET_KEY="sk_test_votre_cle_secrete"
STRIPE_PUBLIC_KEY="pk_test_votre_cle_publique"

# Configuration du mailer
MAILER_DSN=smtp://user:pass@smtp.example.com:port
```

4. **Créer la base de données et exécuter les migrations :**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
``

5. **Démarrer le serveur Symfony :**
```bash
symfony server:start
# OU
php -S localhost:8000 -t public
```

### Configuration du Frontend (React)

1. **Cloner le dépôt :**

git clone https://github.com/hamzajanane/front_ecommerce.git
cd front_ocommerce
```

2. **Installer les dépendances :**
npm install


3. **Configuration des variables d'environnement :**
   - Créer un fichier `.env.local` avec:
```
REACT_APP_API_URL=http://localhost:8000/api
REACT_APP_STRIPE_PUBLIC_KEY=pk_test_votre_cle_publique
```

4. **Démarrer l'application React :**
```bash
npm start

L'application sera disponible à l'adresse: `http://localhost:5173

##  Structure du projet

### Backend (Symfony)

```
back_ocommerce/
├── bin/
├── config/              
├── migrations/          
├── public/              
├── src/
│   ├── Controller/      # Contrôleurs API
│   ├── Entity/          # Entités Doctrine
│   ├── Repository/      # Repositories
│   ├── Service/         # Services métier
│     └── Implementation


### Frontend (React)


front_ocommerce/
├── public/             
├── src/
│   ├── assets/          # Images, styles, etc.
│   ├── components/      # Composants React réutilisables
│   ├── Styles       	 # les fichiers css pour  styles
│   ├── pages/           # Pages de l'application
│   ├── services/        # Services API
│   ├── store/          
│   └── App.tsx
└── ...


##  Utilisateurs et rôles

- **Client** : Peut commander, suivre ses commandes, gérer son profil
- **Administrateur** : Accès au back-office avec tous les droits de gestion


les url des pages
- Gestion des produits : /admin/product

-Gestion des catégories :' /admin/category'

-Tableau de bord : '/admin/dashboard'

-Page d’accueil : /

-Page catalogue des produits : /ProductList

-Page de paiement : /PaymentPage
