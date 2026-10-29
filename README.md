# Green Goodies

Site e-commerce développé avec Symfony 8.1 dans le cadre de ma formation OpenClassrooms.

## Fonctionnalités

### Visiteur
- Affichage du catalogue de produits (page d'accueil) et des pages de détails des produits
- Inscription et connexion (connexion automatique après l'inscription)

### Utilisateur connecté
- Gestion du panier (ajout d'un produit, modification de la quantité, suppression du panier) et validation de la commande
- Activation/désactivation de l'accès API 
- Suppression du compte

### API : 
- Connexion (`/api/login`) 
- Affichage de la liste des produits (`/api/products`)

## Prérequis

- PHP >= 8.4
- Composer
- Node.js (uniquement pour régénérer les fichiers minifiés)

Le projet est configuré pour une utilisation avec MySQL/MariaDB.  
Cependant, vous pouvez tout à fait utiliser un autre SGBD.

## Installation

```bash
git clone https://github.com/laurarzur/green-goodies.git
cd green-goodies
composer install
```

## Configuration

Les variables d'environnement sensibles **ne sont pas versionnées**.  
Le fichier `.env` fourni sur le dépôt ne contient que des valeurs factices (placeholders).  
Les vraies valeurs se placent dans un fichier `.env.local`, qui est ignoré par Git grâce au fichier `.gitignore`.

Créez le fichier `.env.local` à la racine du projet et ajoutez :

```dotenv
# Base de données MySQL
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/nom_de_la_base?serverVersion=8.0&charset=utf8mb4"

# JWT (voir la section suivante)
JWT_PASSPHRASE=choisissez_une_passphrase_de_votre_choix
```

### Clés JWT et passphrase

L'API utilise des tokens JWT (bundle `lexik/jwt-authentication-bundle`), signés avec une paire de clés
(privée et publique). La clé privée est protégée par une **passphrase**.

- Dans `.env`, `JWT_PASSPHRASE` contient une valeur d'exemple.
- Dans `.env.local`, vous définissez **votre propre passphrase**. Elle doit être identique à celle
  utilisée au moment de générer les clés.
- Les clés (`config/jwt/private.pem` et `config/jwt/public.pem`) ne sont **pas** dans le dépôt :
  vous devez les générer.

Une fois `JWT_PASSPHRASE` défini dans `.env.local`, générez les clés :

```bash
php bin/console lexik:jwt:generate-keypair
```

### Base de données

Créez la base de données

```bash
php bin/console doctrine:database:create
```

**Si vous n'utilisez pas MySQL/MariaDB :**  
Supprimez les fichiers du dossier `/migrations` et générez une nouvelle migration

```bash
php bin/console make:migration
```

Exécutez les migrations

```bash
php bin/console doctrine:migrations:migrate
```

Chargez les fixtures

```bash
php bin/console doctrine:fixtures:load
```

Les fixtures créent les produits de démonstration. Les images des produits se trouvent dans
`public/img/products/`.

## Lancer le projet

```bash
symfony server:start
```

Le site est accessible sur `http://localhost:8000`.

## Utilisation de l'API

### Prérequis

L'utilisateur doit activer son accès API depuis son espace « Mon compte ».  
Les clés JWT doivent avoir été générées (voir la section « Configuration »).

### 1. Obtenir un token

- **Méthode et URI** : `POST /api/login`
- **En-tête** : `Content-Type: application/json`
- **Corps de la requête** :

```json
{
    "email": "adresse@exemple.fr",
    "password": "motdepasse"
}
```
- **Réponse** (200) :

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### 2. Récupérer la liste des produits

- **Méthode et URI** : `GET /api/products`
- **En-tête** : `Authorization: Bearer <token>`, où `<token>` est la valeur obtenue à l'étape 1
- **Corps** : aucun
- **Réponse** (200) :

```json
[
  {
    "id": 1, 
    "name": "Produit 1",
    ...
  },
]
```

### Codes de retour en cas d'erreur

| Erreur | Code |
|---|---|
| Identifiants invalides | 401 |
| Token absent ou invalide | 401 |
| Token valide mais accès API désactivé | 403 |

## Green code

- Images optimisées (format WebP, redimensionnées à la taille d'affichage)
- Fichiers CSS et JS minifiés (les sources sont conservées dans `assets/`)

Pour régénérer les fichiers minifiés après une modification :

```bash
npx esbuild frontend/styles/app.css --minify --outfile=assets/styles/app.min.css
npx esbuild frontend/app.js --minify --outfile=assets/app.min.js
```
