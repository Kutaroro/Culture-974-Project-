# 🌴 Culture 974 - Agenda d'Événements

Une application moderne de gestion d'événements culturels pour l'île de la Réunion, développée avec **Symfony 7**.

---

## 🚀 Installation du projet

Suivez ces étapes pour installer le projet localement :

1.  **Cloner le dépôt** :

    ```bash
    git clone <url-du-depot>
    cd Culture-974-Project-
    ```

2.  **Installer les dépendances PHP** :

    ```bash
    composer install
    ```

3.  **Configurer l'environnement** :

    - Copiez le fichier `.env` en `.env.local` si nécessaire.
    - Assurez-vous que l'extension SQLite est activée dans votre `php.ini`.

---

## 🗄️ Base de données & Migrations

Préparez la base de données SQLite :

1.  **Exécuter les migrations** :

    ```bash
    php bin/console doctrine:migrations:migrate --no-interaction
    ```

---

## 🎭 Données de test (Fixtures)

Pour remplir l'application avec des données de démonstration (catégories, événements, inscriptions) :

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

_Cette commande réinitialise la base de données et insère 10 catégories, 50 événements et 100 inscriptions._

---

## ✨ Fonctionnalités disponibles

### 📅 Espace Public

- **Accueil** : Présentation des événements à venir.
- **Catalogue d'Événements** : Liste complète des événements avec filtrage par categories
- **Détails** : Consultation des informations détaillées d'un événement.
- **Inscription** : Formulaire permettant aux utilisateurs de réserver des places.

### 🔐 Espace Administration

Accès via `/admin/inscriptions` (Lien "Administration" dans le menu).

- **Authentification Sécurisée** : Système robuste sans table utilisateur (in-memory).
- **Gestion des Inscriptions** : Liste complète des réservations par événement.
- **Suivi des Places** : Calcul automatique du total des places réservées.
- **Modération** : Possibilité de consulter et supprimer des inscriptions.

**Identifiants par défaut :**

- **Utilisateur** : `admin`
- **Mot de passe** : `admin`

---

## 🛠️ Stack Technique

- **Backend** : Symfony 7.4 + PHP 8.2
- **Database** : SQLite / Doctrine ORM
- **Frontend** : Twig / AssetMapper / Stimulus / Turbo
- **Security** : Symfony Security (In-memory provider)

---

_Développé avec ❤️ pour la culture réunionnaise._
