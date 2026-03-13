# Laravel Auth API

API RESTful d'authentification et de gestion de profil, construite avec **Laravel 11** et **Laravel Sanctum**.

---

## Stack technique

| Outil | Rôle |
|---|---|
| Laravel 11 | Framework PHP |
| Laravel Sanctum | Authentification par token |
| MySQL / SQLite | Base de données |
| Postman | Test et documentation de l'API |

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/a-oirgari/sprint7brief1
cd laravel-auth-api
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Éditer `.env` avec vos paramètres de base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_auth_api
DB_USERNAME=root
DB_PASSWORD=
```

> **SQLite (développement rapide)** : remplacez simplement par `DB_CONNECTION=sqlite` et créez le fichier `database/database.sqlite`.

### 4. Installer Sanctum

```bash
composer require laravel/sanctum
```

### 5. Exécuter les migrations

```bash
php artisan migrate
```

### 6. Lancer le serveur

```bash
php artisan serve
```

L'API sera disponible sur `http://localhost:8000`.

---

## Documentation API

La documentation complète est fournie sous forme de **collection Postman** :

📄 `docs/postman_collection.json`

### Importer dans Postman

1. Ouvrir Postman
2. Cliquer sur **Import** (haut gauche)
3. Sélectionner le fichier `docs/postman_collection.json`
4. La collection **Laravel Auth API** apparaît dans le panneau de gauche

### Configurer le token automatiquement

La requête **Login** contient un script de test qui enregistre automatiquement le token dans la variable de collection `{{token}}`. Toutes les routes protégées utilisent cette variable.

---

## Routes disponibles

### Publiques

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/register` | Créer un compte |
| POST | `/api/login` | Se connecter (retourne un token) |

### Protégées (Bearer token obligatoire)

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/logout` | Se déconnecter |
| GET | `/api/me` | Consulter son profil |
| PUT | `/api/me` | Modifier son profil (name, email) |
| PUT | `/api/me/password` | Changer son mot de passe |
| DELETE | `/api/me` | Supprimer son compte |

### Passer le token dans Postman

Dans l'onglet **Authorization** de la requête :
- Type : `Bearer Token`
- Token : `{{token}}` (ou coller la valeur manuellement)

---

## Codes de réponse HTTP

| Code | Signification |
|---|---|
| 200 | Succès |
| 201 | Ressource créée |
| 401 | Non authentifié (token absent ou invalide) |
| 422 | Données invalides (validation échouée) |

---

## Règles métier importantes

- Les mots de passe sont **hachés** avec bcrypt (jamais stockés en clair).
- Un utilisateur ne peut accéder et modifier **que son propre profil**.
- Après un **changement de mot de passe**, tous les tokens sont révoqués — il faut se reconnecter.
- Après une **déconnexion**, le token utilisé est révoqué — il ne fonctionnera plus.
- Un **email** doit être unique dans la base de données.

---

## Scénario de test complet

Voici le flux à suivre pour valider tous les cas du brief :

```
1. POST /api/register          → Créer un compte
2. POST /api/login             → Récupérer le token
3. GET  /api/me  (sans token)  → Doit retourner 401 Unauthorized
4. GET  /api/me  (avec token)  → Succès, profil retourné
5. PUT  /api/me  (avec token)  → Modifier le nom ou l'email
6. PUT  /api/me/password       → Changer le mot de passe (invalide l'ancien token)
7. POST /api/logout            → Déconnecter (avec le nouveau token obtenu après re-login)
8. GET  /api/me  (ancien token)→ Doit retourner 401 Unauthorized
```

---

## Structure des fichiers

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       # register, login, logout
│   │   └── ProfileController.php    # show, update, updatePassword, destroy
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── LoginRequest.php
│       ├── UpdateProfileRequest.php
│       └── UpdatePasswordRequest.php
├── Models/
│   └── User.php
bootstrap/
└── app.php                          # Gestion de l'erreur 401 en JSON
routes/
└── api.php                          # Toutes les routes de l'API
database/
└── migrations/
    ├── ..._create_users_table.php
    └── ..._create_personal_access_tokens_table.php
docs/
└── postman_collection.json          # Documentation Postman
```