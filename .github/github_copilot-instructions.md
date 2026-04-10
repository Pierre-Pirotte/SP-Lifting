# SP-Lifting — Copilot Instructions

## 1. Contexte général du projet

SP-Lifting est un site web spécialisé dans le suivi et l'enregistrement de performances sportives, dédié exclusivement aux pratiquants de **powerlifting** et de **street-lifting**. Ces deux disciplines de force ont pour objectif l'amélioration constante de la force maximale sur une répétition (1RM), contrairement à la musculation classique orientée esthétique.

Le projet est développé dans le cadre d'un **TFE (Travail de Fin d'Études) de formation webdéveloppeur**, prévu pour **début juillet 2026**. Le développeur souhaite **coder lui-même** un maximum, Copilot intervient en soutien (aide, suggestions, review, debugging) et non en remplacement.

Le repository GitHub est : `Pierre-Pirotte/SP-Lifting`

### État actuel du projet
- Système de connexion/déconnexion utilisateur fonctionnel
- Utilisateur test `ROLE_USER` (accès partie publique uniquement)
- Utilisateur test `ROLE_ADMIN` (accès partie publique + interface d'administration)
- Base de données initialisée avec les 2 disciplines et les 7 exercices associés

---

## 2. Stack technique

| Couche | Technologie |
|---|---|
| Backend | **Symfony 8.0** (architecture MVC, ORM Doctrine, sécurité intégrée) |
| Frontend | **Bootstrap 5.3** (responsive, composants prêts à l'emploi) |
| Frontend dynamique | **Vue.js** (prévu pour les fonctionnalités Should Have : graphiques, infobulles, temps réel) |
| Base de données | **MySQL 8.0** |
| Gestion dépendances PHP | Composer |
| Assets JS/CSS | npm + Webpack Encore |
| CLI | Symfony CLI |
| Versioning | Git / GitHub |
| Templates | Twig |
| Tests | PHPUnit + tests fonctionnels Symfony (version finale) |

---

## 3. Architecture et niveaux d'accès

Le site propose **3 niveaux d'accès** :

### 3.1 Pages publiques (sans connexion)
- Page d'accueil (présentation du site, aperçu des fonctionnalités)
- Documentation sur les deux disciplines (accessible via navbar)
- Formulaire d'inscription et de connexion
- Formulaire de contact (accessible via navbar ET footer)

### 3.2 Espace utilisateur connecté (`ROLE_USER`)
- **Dashboard** : vue d'ensemble synthétique, dernières performances, indicateurs clés, messages de motivation (ex : "meilleure performance ce mois-ci")
- **Gestion du profil** : modification email, mot de passe, pseudo, poids, taille, date de naissance, discipline(s) pratiquée(s), suppression du compte
- **Enregistrement de performances** : sélection discipline (optionnelle), exercice, date, type d'entraînement, ajout de séries (répétitions, charge/lest, RPE, temps de repos, marquage échauffement)
- **Historique des performances** : affichage chronologique, filtres avancés (exercice, discipline, période, type), détails complets par performance, modification/suppression
- **Statistiques** : PR par exercice, 1RM estimé, volume total, fréquence d'entraînement, ratio poids/force, exercice le plus pratiqué, courbes de progression (graphiques — Should Have)

### 3.3 Interface d'administration (`ROLE_ADMIN`)
- **Gestion des utilisateurs** : liste complète, tri chronologique (bouton inversion), recherche par email/pseudo, soft-delete
- **Gestion des exercices** : ajout, modification, suppression (avec protection si lié à des performances)
- **Gestion des disciplines** : même fonctionnement que la gestion des exercices
- **Statistiques globales** : nombre d'utilisateurs, performances enregistrées, exercices/disciplines disponibles, statistiques d'utilisation
- Bouton **"Administration"** visible dans la navbar uniquement pour les admins
- Bouton **"Retour au site"** dans la navbar de l'interface admin

---

## 4. Base de données — Architecture complète

La BDD MySQL contient **8 tables relationnelles** organisées sur 3 axes :
1. Gestion des utilisateurs : `users`, `user_disciplines`
2. Référentiel sportif : `disciplines`, `exercises`, `exercise_disciplines`
3. Suivi des performances : `performances`, `performance_sets`, `programs`

---

### 4.1 Table `users`

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE | Identifiant de connexion |
| `password_hash` | VARCHAR(255) | NOT NULL | Mot de passe hashé (jamais en clair) |
| `display_name` | VARCHAR(120) | NULL | Pseudo (NULL → affichage email) |
| `roles` | JSON | NULL | Ex: `["ROLE_USER"]` ou `["ROLE_ADMIN"]` |
| `sex` | ENUM('M','F','X') | NOT NULL DEFAULT 'X' | Genre (X = non spécifié) |
| `birthdate` | DATE | NULL | Date de naissance (calcul âge) |
| `weight_kg` | DECIMAL(6,2) | NULL | Poids en kg (ratio poids/force, IMC) |
| `height_cm` | SMALLINT | NULL | Taille en cm (calcul IMC) |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création compte |
| `updated_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Dernière modification |
| `deleted_at` | DATETIME | NULL | Soft-delete (NULL = actif, date = supprimé) |

**Notes importantes :**
- `birthdate`, `weight_kg`, `height_cm` sont optionnels (données personnelles sensibles RGPD)
- Le soft-delete via `deleted_at` conserve les données mais désactive le compte
- Un compte soft-deleted ne peut plus se connecter

---

### 4.2 Table `performances`

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `user_id` | INT UNSIGNED | NOT NULL, FK → users.id | Propriétaire de la performance |
| `exercise_id` | INT UNSIGNED | NOT NULL, FK → exercises.id | Exercice effectué |
| `discipline_id` | INT UNSIGNED | NULL, FK → disciplines.id | Discipline (optionnelle) |
| `date_performed` | DATE | NOT NULL | Date de la performance |
| `entry_type` | ENUM('classic','test','1rm') | NOT NULL DEFAULT 'classic' | Type d'entraînement |
| `note` | TEXT | NULL | Commentaire libre |
| `source` | ENUM('manual','import') | NOT NULL DEFAULT 'manual' | Origine de la saisie |
| `privacy_level` | ENUM('private','public') | NOT NULL DEFAULT 'private' | Visibilité (futur partage public) |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création |
| `updated_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Dernière modification |

**Notes importantes :**
- `discipline_id` est NULL car optionnel (l'exercice suffit à identifier la performance)
- `entry_type = '1rm'` → les séries de travail sont limitées à 1 répétition maximum
- `privacy_level` prépare la fonctionnalité Could Have de partage public
- `source = 'import'` prépare la fonctionnalité Should Have d'export/import CSV

---

### 4.3 Table `performance_sets`

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `performance_id` | INT UNSIGNED | NOT NULL, FK → performances.id | Performance parente |
| `set_index` | SMALLINT UNSIGNED | NOT NULL | Ordre de la série (1, 2, 3...) |
| `reps` | SMALLINT | NULL | Nombre de répétitions (NULL si non applicable) |
| `weight` | DECIMAL(6,2) | NULL | Charge en kg (0 = poids de corps, NULL si non applicable) |
| `rpe` | SMALLINT | NULL | RPE 1-10 (Rate of Perceived Exertion) |
| `rest_seconds` | INT | NULL | Temps de repos en secondes |
| `is_warmup` | TINYINT(1) | NOT NULL DEFAULT 0 | 1 = échauffement, 0 = série de travail |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création |

**Notes importantes :**
- Pas de `updated_at` (série = fait historique figé)
- `weight = 0` pour les exercices au poids de corps non lestés
- `reps` et `weight` peuvent être NULL pour des disciplines futures (ex : exercices isométriques)
- `is_warmup` permet de filtrer les séries d'échauffement dans les statistiques

---

### 4.4 Table `disciplines`

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `slug` | VARCHAR(100) | NOT NULL, UNIQUE | Identifiant URL (ex: "powerlifting") |
| `name` | VARCHAR(150) | NOT NULL | Nom affichable (ex: "Powerlifting") |
| `description` | TEXT | NULL | Description brève |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création |
| `updated_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Dernière modification |

**Données initiales insérées :**
- Powerlifting (slug: `powerlifting`)
- Street-Lifting (slug: `street-lifting`)

---

### 4.5 Table `exercises`

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `slug` | VARCHAR(150) | NOT NULL, UNIQUE | Identifiant URL (ex: "bench-press") |
| `name` | VARCHAR(200) | NOT NULL | Nom affichable (ex: "Développé couché") |
| `category` | ENUM('compound','bodyweight','accessory') | NOT NULL DEFAULT 'accessory' | Catégorie |
| `default_unit` | ENUM('kg','lbs') | NOT NULL DEFAULT 'kg' | Unité par défaut |
| `default_reps_range` | VARCHAR(20) | NULL | Fourchette répétitions (ex: "1-5") |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création |
| `updated_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Dernière modification |

**Catégories :**
- `compound` : Squat, Bench Press, Deadlift (powerlifting)
- `bodyweight` : Dips, Pull-ups, Muscle-ups (street-lifting)
- `accessory` : futurs exercices d'isolation/assistance

**Données initiales insérées (7 exercices) :**
- Powerlifting (3) : Squat, Bench Press (Développé couché), Deadlift (Soulevé de terre)
- Street-Lifting (4) : Dips, Pull-ups (Tractions), Muscle-ups, Squat (partagé avec powerlifting)

---

### 4.6 Table `exercise_disciplines` (liaison many-to-many)

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `exercise_id` | INT UNSIGNED | NOT NULL, FK → exercises.id | Exercice |
| `discipline_id` | INT UNSIGNED | NOT NULL, FK → disciplines.id | Discipline |
| `metadata` | JSON | NULL | Ex: `{"default_sets": 5, "recommended_rest_seconds": 180}` |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création du lien |
| `updated_at` | DATETIME | NULL | Dernière modification (optionnel) |

**Notes importantes :**
- Clé primaire composite `(exercise_id, discipline_id)` → pas de doublons
- Le Squat est lié aux deux disciplines (powerlifting ET street-lifting)
- `metadata` JSON permet des paramètres spécifiques à chaque paire exercice-discipline

---

### 4.7 Table `user_disciplines` (liaison many-to-many)

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `user_id` | INT UNSIGNED | NOT NULL, FK → users.id | Utilisateur |
| `discipline_id` | INT UNSIGNED | NOT NULL, FK → disciplines.id | Discipline |
| `preferred` | TINYINT(1) | NOT NULL DEFAULT 1 | 1 = pratiquée activement, 0 = désactivée |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création du lien |

**Notes importantes :**
- Clé primaire composite `(user_id, discipline_id)` → pas de doublons
- `preferred = 0` désactive temporairement sans supprimer la relation
- Les disciplines préférées sont affichées en priorité dans le formulaire d'enregistrement

---

### 4.8 Table `programs` (Could Have — version future)

| Champ | Type | Contraintes | Description |
|---|---|---|---|
| `id` | INT UNSIGNED AUTO_INCREMENT | PK, NOT NULL | Identifiant unique |
| `user_id` | INT UNSIGNED | NULL, FK → users.id | Propriétaire (NULL = programme global admin) |
| `discipline_id` | INT UNSIGNED | NULL, FK → disciplines.id | Discipline liée (optionnelle) |
| `title` | VARCHAR(255) | NOT NULL | Titre du programme |
| `description` | TEXT | NULL | Description, objectifs, pré-requis |
| `payload` | JSON | NULL | Structure complète (semaines, séances, exercices, séries...) |
| `status` | VARCHAR(20) | NULL DEFAULT 'draft' | État : draft / active / archived |
| `created_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP | Date création |
| `updated_at` | DATETIME | NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Dernière modification |

**Notes importantes :**
- `user_id = NULL` → programme exemple global créé par un admin
- `payload = NULL` autorisé pour les brouillons (mode draft)
- `status` utilise VARCHAR (pas ENUM) pour flexibilité future
- **Fonctionnalité Could Have**, non prioritaire pour le TFE

---

## 5. Priorisation MoSCoW

### ✅ Must Have (priorité absolue)
1. Base de données structurée (8 tables — déjà définie)
2. Authentification : inscription, connexion/déconnexion, sécurité de base
3. Distinction `ROLE_USER` / `ROLE_ADMIN`
4. Gestion du profil utilisateur (email, mot de passe, poids, taille, disciplines pratiquées)
5. **Enregistrement des performances** (cœur de métier du site)
6. Historique des performances avec filtres (date, discipline, exercice, type)
7. Affichage des statistiques (PR, nombre d'entraînements, niveau, estimation prochains records)
8. Pages de documentation publiques (powerlifting et street-lifting, accessible sans connexion)
9. Interface d'administration (gestion users, exercices, disciplines, stats globales)

### 🔶 Should Have (après Must Have complété)
- Modification/suppression d'une performance enregistrée
- Graphiques avec courbes de progression (Vue.js)
- Photo de profil utilisateur
- Info-bulles conseils lors de la saisie d'une performance
- Filtres avancés enrichis (statistiques et historique)
- Comparaison anonyme avec la communauté
- Export des données en CSV (lien RGPD — droit à la portabilité)
- Messages de motivation dans le dashboard ("meilleure performance ce mois-ci")
- Formulaire de contact (admin)

### 🔷 Could Have (bonus si tout le reste est terminé)
- Création de programmes d'entraînement (manuel ou généré automatiquement)
- Partage public de performances (via `privacy_level` dans la table `performances`)
- Tutoriel d'utilisation du profil
- Notifications de rappels de régularité
- Personnalisation de l'apparence du profil (thèmes)
- Quiz sur la documentation
- Vidéos tutoriels dans la documentation

### ❌ Won't Have (hors scope TFE)
- Planification d'entraînements futurs avec calendrier
- Application mobile native iOS/Android
- Système de messagerie entre utilisateurs
- API publique pour développeurs tiers
- Système de paiement/abonnements premium

---

## 6. User Stories (résumé des 24 US)

| ID | Rôle | Action | Statut MoSCoW |
|---|---|---|---|
| US-01 | Visiteur | Inscription avec données valides | Must Have |
| US-02 | Visiteur | Inscription avec email déjà utilisé → erreur | Must Have |
| US-03 | Visiteur | Inscription avec mot de passe invalide → erreur | Must Have |
| US-04 | Utilisateur | Connexion avec identifiants valides | Must Have |
| US-05 | Utilisateur | Connexion avec mot de passe incorrect → erreur | Must Have |
| US-06 | Utilisateur connecté | Déconnexion | Must Have |
| US-07 | Utilisateur connecté | Modification infos personnelles (poids, taille, naissance) | Must Have |
| US-08 | Utilisateur connecté | Modification de l'adresse email | Must Have |
| US-09 | Utilisateur connecté | Modification du mot de passe | Must Have |
| US-10 | Utilisateur connecté | Sélection des disciplines pratiquées | Must Have |
| US-11 | Utilisateur connecté | Enregistrement d'une performance (classic/test/1RM) | Must Have |
| US-12 | Utilisateur connecté | Enregistrement sans exercice sélectionné → erreur | Must Have |
| US-13 | Utilisateur connecté | Enregistrement avec charge négative → erreur | Must Have |
| US-14 | Utilisateur connecté | Modification d'une performance existante | Should Have |
| US-15 | Utilisateur connecté | Suppression d'une performance | Should Have |
| US-16 | Utilisateur connecté | Consultation de l'historique complet | Must Have |
| US-17 | Utilisateur connecté | Filtrage avancé de l'historique | Must Have |
| US-18 | Utilisateur connecté | Consultation des statistiques globales | Must Have |
| US-19 | Visiteur / Utilisateur | Consultation documentation disciplines | Must Have |
| US-20 | Visiteur / Utilisateur | Envoi message via formulaire de contact | Should Have |
| US-21 | Utilisateur simple | Tentative accès admin → erreur 403 | Must Have |
| US-22 | Administrateur | Accès interface d'administration | Must Have |
| US-23 | Administrateur | Gestion utilisateurs (liste, recherche, soft-delete) | Must Have |
| US-24 | Administrateur | Gestion exercices (ajout, modification, suppression) | Must Have |

---

## 7. Règles métier importantes

### Authentification & Sécurité
- Mot de passe : **minimum 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial**
- Les mots de passe sont hashés avec **bcrypt ou argon2** (jamais en clair)
- Message d'erreur de connexion générique : **"Identifiants incorrects"** (ne pas préciser si c'est l'email ou le mot de passe)
- Protection CSRF sur tous les formulaires (tokens Symfony)
- Cookies de session en **httpOnly**
- Protection contre SQL injection (Doctrine ORM — requêtes préparées)
- Protection XSS via **échappement automatique Twig**
- Validation **côté serveur obligatoire** (ne jamais se fier uniquement au JS côté client)

### Enregistrement de performances
- `entry_type = '1rm'` → les séries de travail sont limitées à **1 répétition maximum**
- La `discipline_id` est **optionnelle** dans une performance
- Les séries peuvent être marquées comme **échauffement** (`is_warmup = 1`)
- `weight = 0` pour les exercices au poids de corps **sans lest**
- La charge doit être **positive** (validation obligatoire)
- La date de performance ne peut pas être **dans le futur**
- Nombre de répétitions et séries : doit être un **nombre entier positif cohérent**

### Gestion des exercices (admin)
- Le `slug` d'un exercice doit être **unique**
- Le `name` d'un exercice doit être **unique**
- Un exercice **ne peut pas être supprimé** s'il est lié à des performances existantes
- (Prévu) Champ `is_visible` pour masquer/désactiver un exercice sans le supprimer

### Gestion des utilisateurs (admin)
- La suppression d'un utilisateur est un **soft-delete** : remplissage du champ `deleted_at`
- Un utilisateur soft-deleted **ne peut plus se connecter**
- Les données sont **conservées** après suppression (historique)
- Confirmation obligatoire avant toute suppression (pour éviter les accidents)

### Statistiques calculées
- **PR (Personal Record)** = charge maximale enregistrée sur 1 répétition pour chaque exercice
- **1RM estimé** = calculé en fonction des performances disponibles
- **Volume total** = somme de (charge × répétitions) pour toutes les séries de travail
- **Ratio poids/force** = charge actuelle / poids corporel de l'utilisateur
- **Fréquence** = nombre de séances par semaine/mois

---

## 8. Disciplines et exercices (données initiales)

### Powerlifting (3 exercices — category: `compound`)
| Exercice | Slug | Muscles principaux |
|---|---|---|
| Squat | `squat` | Quadriceps, fessiers, ischio-jambiers, lombaires |
| Bench Press (Développé couché) | `bench-press` | Pectoraux, triceps, deltoïdes antérieurs |
| Deadlift (Soulevé de terre) | `deadlift` | Chaîne postérieure complète |

### Street-Lifting (4 exercices)
| Exercice | Slug | Category | Muscles principaux |
|---|---|---|---|
| Dips | `dips` | bodyweight | Pectoraux, triceps, deltoïdes antérieurs |
| Pull-ups (Tractions) | `pull-ups` | bodyweight | Dorsaux, biceps, trapèzes |
| Muscle-ups | `muscle-ups` | bodyweight | Haut du corps complet |
| Squat | `squat` | compound | Partagé avec powerlifting |

**Note :** Le Squat est lié aux **deux disciplines** via `exercise_disciplines`.

---

## 9. Tests — règles et stratégie

### Tests manuels (pendant tout le développement)
Tester systématiquement :
- Les **cas de succès** (happy path)
- Les **cas d'erreur** (validation, accès non autorisé, données invalides)

### Tests automatisés (version finale)
- **PHPUnit** pour les tests unitaires
- **Tests fonctionnels Symfony** pour les scénarios complets

### Points de sécurité à tester obligatoirement
- Injection SQL : tester avec caractères spéciaux dans les formulaires
- XSS : tester `<script>alert('XSS')</script>` dans les champs texte
- CSRF : vérifier que les requêtes sans token valide sont rejetées
- Accès non autorisé : tester qu'un `ROLE_USER` ne peut jamais accéder aux routes `/admin`
- Validation serveur : contourner le JS et tester directement les requêtes HTTP

---

## 10. Conventions de développement

- **Architecture** : MVC Symfony, controllers, entities Doctrine, repositories
- **ORM** : Doctrine (pas de SQL brut sauf cas exceptionnel justifié)
- **Templates** : Twig (échappement automatique activé)
- **Routing** : annotations ou attributs PHP 8 sur les controllers
- **Sécurité** : `security.yaml` Symfony pour la gestion des rôles et firewall
- **Formulaires** : Symfony Form Component avec validation via contraintes (`Assert`)
- **Langue du code** : anglais (noms de variables, méthodes, entités)
- **Langue de l'interface** : français
- **Responsive** : Bootstrap 5.3, design mobile-first
- **Vue.js** : uniquement pour les fonctionnalités Should Have (graphiques, infobulles dynamiques)

---

## 11. Architecture prévue de l'application (routes principales)

### Publiques
- `GET /` — Page d'accueil
- `GET /register` — Formulaire d'inscription
- `GET/POST /login` — Formulaire de connexion
- `POST /logout` — Déconnexion
- `GET /documentation/powerlifting` — Documentation powerlifting
- `GET /documentation/street-lifting` — Documentation street-lifting
- `GET/POST /contact` — Formulaire de contact

### Espace utilisateur (ROLE_USER requis)
- `GET /profile` — Dashboard
- `GET/POST /profile/edit` — Modification du profil
- `GET/POST /profile/performances/new` — Enregistrement performance
- `GET /profile/performances` — Historique avec filtres
- `GET /profile/performances/{id}` — Détail d'une performance
- `GET/POST /profile/performances/{id}/edit` — Modification (Should Have)
- `POST /profile/performances/{id}/delete` — Suppression (Should Have)
- `GET /profile/statistics` — Statistiques

### Interface admin (ROLE_ADMIN requis)
- `GET /admin` — Dashboard admin
- `GET /admin/users` — Gestion utilisateurs
- `POST /admin/users/{id}/delete` — Soft-delete utilisateur
- `GET /admin/exercises` — Gestion exercices
- `GET/POST /admin/exercises/new` — Ajout exercice
- `GET/POST /admin/exercises/{id}/edit` — Modification exercice
- `POST /admin/exercises/{id}/delete` — Suppression exercice
- `GET /admin/disciplines` — Gestion disciplines
- `GET/POST /admin/disciplines/new` — Ajout discipline
- `GET/POST /admin/disciplines/{id}/edit` — Modification discipline
- `POST /admin/disciplines/{id}/delete` — Suppression discipline
- `GET /admin/statistics` — Statistiques globales du site