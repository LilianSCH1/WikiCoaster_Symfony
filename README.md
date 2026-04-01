# WikiCoaster - TP3

## 1) Vision projet
WikiCoaster est une application web collaborative autour des montagnes russes et des parcs, construite avec Symfony. L'objectif est de proposer une base applicative maintenable, testable et prête pour des itérations fonctionnelles rapides.

## 2) Objectifs
- Mettre en place une application Symfony stable avec persistance MySQL.
- Structurer un socle front moderne (Webpack Encore, Stimulus, Turbo, Tailwind/DaisyUI).
- Garantir un démarrage local simple pour l'equipe.
- Industrialiser les migrations Doctrine pour fiabiliser les evolutions de schema.

## 3) Perimetre actuel (MVP)
- Gestion des entites coeur via Doctrine ORM.
- Interface web Twig + assets compiles.
- Authentification et securite Symfony.
- Jeux de fixtures et tests de base.

## 4) Parties prenantes
- Product Owner: definie les priorites fonctionnelles.
- Developpeurs: implementation backend/frontend, qualite du code.
- QA/Recette: validation des parcours critiques.
- Responsable technique: architecture, dette technique, performance.

## 5) Pre-requis locaux
- Windows avec WampServer (MySQL + phpMyAdmin).
- PHP 8.5 (version projet: fichier .php-version).
- Composer 2.x.
- Node.js LTS + npm.
- (Optionnel) Symfony CLI pour la commande symfony serve.

## 6) Configuration base de donnees
Le projet est configure par defaut en MySQL local:

- Fichier: .env
- Variable: DATABASE_URL
- Valeur actuelle:

```dotenv
DATABASE_URL="mysql://root@127.0.0.1:3306/main_tp3?serverVersion=8.0.32&charset=utf8mb4"
```

Si votre WampServer utilise un mot de passe root, adaptez DATABASE_URL dans .env.local (recommande) plutot que de modifier .env.

## 7) Runbook de demarrage (local)

### Demarrage rapide (workflow Git)
```bash
git clone LIEN_DU_DEPOT
cd WikiCoaster-tp3
git checkout -b tp1 origin/tp1

composer install
npm install

# Option Docker
docker-compose up -d

# Build assets
npm run build
# ou
npm run watch

# Lancer Symfony
symfony serve
```

Note: selon votre installation Docker, la commande peut etre `docker compose up -d`.

### Etape A - Installer les dependances
```bash
composer install
npm install
```

### Etape B - Preparer la base
```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate -n
```

### Etape C - Compiler les assets
```bash
npm run dev
```

### Etape D - Lancer le serveur
Option 1 (recommande):
```bash
symfony serve
```

Option 2 (si Symfony CLI absent):
```bash
php -S 127.0.0.1:8000 -t public
```

Application accessible sur:
- http://127.0.0.1:8000

## 8) Commandes utiles
- Etat des migrations:
```bash
php bin/console doctrine:migrations:status
```

- Executer les tests:
```bash
php bin/phpunit
```

- Construire les assets de production:
```bash
npm run build
```