# Travel Paradise

Travel Paradise est une application web moderne pour la gestion des visites touristiques, permettant aux guides de gérer leurs visites et aux administrateurs de superviser l'ensemble du système.

## Fonctionnalités

- Gestion des lieux touristiques
- Gestion des visites guidées
- Gestion des guides
- Gestion des remboursements
- Tableau de bord statistiques
- Système de logs
- Interface d'administration
- Interface guide

## Technologies utilisées

### Backend
- PHP 8.1
- Symfony 6.4
- MySQL 8.0
- JWT Authentication
- Doctrine ORM

### Frontend
- React 18
- TypeScript
- Material-UI
- Recharts
- Axios
- React Router

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Node.js 16 ou supérieur
- MySQL 8.0 ou supérieur
- Symfony CLI

## Installation

1. Cloner le repository :
```bash
git clone https://github.com/votre-username/travel-paradise.git
cd travel-paradise
```

2. Installer les dépendances backend :
```bash
cd backend
composer install
```

3. Configurer la base de données :
- Créer une base de données MySQL
- Copier le fichier `.env` en `.env.local` et configurer les variables d'environnement
- Exécuter les migrations :
```bash
php bin/console doctrine:migrations:migrate
```

4. Installer les dépendances frontend :
```bash
cd ../frontend
npm install
```

5. Configurer le frontend :
- Copier le fichier `.env` en `.env.local` et configurer les variables d'environnement

## Démarrage

1. Démarrer le serveur backend :
```bash
cd backend
symfony server:start
```

2. Démarrer le serveur frontend :
```bash
cd frontend
npm start
```

L'application sera accessible à l'adresse http://localhost:3000

## Structure du projet

```
travel-paradise/
├── backend/                 # Application Symfony
│   ├── config/             # Configuration Symfony
│   ├── src/                # Code source PHP
│   │   ├── Controller/     # Contrôleurs
│   │   ├── Entity/         # Entités Doctrine
│   │   ├── Repository/     # Repositories Doctrine
│   │   ├── Service/        # Services
│   │   └── ...
│   └── ...
├── frontend/               # Application React
│   ├── public/            # Fichiers statiques
│   ├── src/               # Code source TypeScript
│   │   ├── components/    # Composants React
│   │   ├── contexts/      # Contextes React
│   │   ├── services/      # Services API
│   │   └── ...
│   └── ...
└── ...
```

## Tests

### Backend
```bash
cd backend
php bin/phpunit
```

### Frontend
```bash
cd frontend
npm test
```

## Déploiement

1. Build du frontend :
```bash
cd frontend
npm run build
```

2. Déploiement du backend :
```bash
cd backend
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
```

## Contribution

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

Pour rendre le projet complètement fonctionnel, voici les prochaines étapes à suivre :
Frontend :
Installer les dépendances React nécessaires
Implémenter les composants manquants
Configurer le routage
Mettre en place la gestion d'état (Redux/Context)
Backend :
Vérifier la configuration de la base de données
Tester les endpoints API
Vérifier la gestion des médias
Tester les fonctionnalités de statistiques
Intégration :
Configurer CORS
Tester l'authentification
Vérifier la communication frontend-backend

Créer les migrations de base de données
Ajouter la documentation API avec API Platform
Ajouter des tests unitaires et fonctionnels