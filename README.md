# Pause WI-FI

**Pause WI-FI** est un site web permettant de localiser les spots Wi-Fi gratuits à Paris.
Le projet utilise une architecture MVC modifiée et s'appuie sur un jeu de données open data fourni par la Ville de Paris.

---

## Fonctionnalités principales

- Recherche multi-critères de spots Wi-Fi
- Carte interactive avec localisation des spots (Leaflet.js)
- Espace utilisateur avec inscription et connexion
- Ajout de spots en favoris
- Espace administrateur sécurisé avec CRUD sur les spots
- Téléversement de fichiers possible
- Mots de passe sécurisés (hash)
- Sécurité renforcée (requêtes préparées, validation…)

---

## Technologies utilisées

- **Frontend** : HTML5, CSS3, JavaScript, Leaflet.js
- **Backend** : PHP (MVC natif)
- **Base de données** : MySQL
- **Autres** : AJAX, Sessions PHP, PDO, Open Data (CSV importé)

---

## Structure du projet
```bash
PauseWiFi/
│
├── www/                        # Racine du site web
│   ├── index.php                   # Page principale (routeur du site)
│   └── logout.php                  # Déconnexion
│
│   ├── config/                 # Configuration
│   │   ├── mail.php                # Configuration de PHPMailer 
│   │   └── db.php                  # Connexion à la base de données
│
│   ├── actions/                # Traitement PHP
│   │   ├── login.php                  # Connexion
│   │   ├── register.php               # Inscription
│   │   ├── edit-profile.php           # Modification du profil
│   │   ├── change-password.php        # Changement de mot de passe 
│   │   ├── forgot-password.php        # Mot de passe oublié
│   │   ├── reset-password.php         # Réinitialisation du mot de passe
│   │   ├── upload-profile-picture.php # Upload de photo de profil
│   │   ├── toggle-favorite.php        # Ajout/suppression des favoris
│   │   └── admin-actions.php          # Actions d’administration (CRUD)
│
│   ├── models/                 # Modèles PHP (accès aux données)
│   │   ├── WifiSpot.php            # Spots Wi-Fi
│   │   ├── User.php                # Utilisateurs
│   │   ├── ActivityLog.php         # Logs d'activité
│   │   └── Favorite.php            # Favoris
│
│   ├── vendor/                 # Dépendances (Composer, PHPMailer…)
│   │   ├── composer/
│   │   └── phpmailer/
│
│   ├── uploads/                # Uploads des utilisateurs
│   │   ├── profiles/               # Photos de profil
│   │   └── .htaccess               # Sécurité du dossier
│
│   ├── views/                  # Vues HTML
│   │   ├── partials/           
│   │   │   ├── header.php          # En-tête
│   │   │   └── footer.php          # Pied de page
│   │   ├── list.php                # Liste des spots avec filtres
│   │   ├── map.php                 # Carte interactive
│   │   ├── home.php                # Page d’accueil
│   │   ├── wifi_detail.php         # Détail d’un spot
│   │   ├── login.php               # Connexion
│   │   ├── register.php            # Inscription
│   │   ├── about.php               # À propos
│   │   ├── contact.php             # Contact
│   │   ├── forgot_password.php     # Mot de passe oublié
│   │   ├── admin_dashboard.php     # Dashboard admin
│   │   ├── edit-spot.php           # Ajout/édition de spot (CRUD admin)
│   │   ├── profile.php             # Profil utilisateur
│   │   └── not_found.php           # Erreur 404
│
│   ├── assets/                 # Fichiers publics
│   │   ├── css/
│   │   │   ├── global.css
│   │   │   ├── home.css
│   │   │   ├── auth.css
│   │   │   ├── profile.css
│   │   │   ├── header_footer.css
│   │   │   ├── edit-spot.css
│   │   │   ├── admin.css
│   │   │   ├── favorites.css
│   │   │   ├── contact.css
│   │   │   ├── map.css
│   │   │   ├── list.css
│   │   │   ├── not_found.css
│   │   │   ├── about.css
│   │   │   └── spot-detail.css
│   │   ├── js/
│   │   │   ├── map.js
│   │   │   ├── search.js
│   │   │   ├── mobile-menu.js
│   │   │   ├── favorites.js
│   │   │   ├── form-validation.js
│   │   │   └── admin-tabs.js
│   │   └── img/
│   │       └── logo.png
│
├── doc/                        # Documentation
│   ├── backlog.md                  # Backlog et User Stories
│   ├── schema_bdd.png              # Schéma de la BDD
│   └── utilisation.md              # Guide d’utilisation
│
├── pause_wifi.sql              # Fichier SQL (structure + données)
└── README.md                   # Présentation du projet
```
---

## Ressources

Projet réalisé dans un cadre pédagogique.  
Données issues du site [opendata.paris.fr](https://opendata.paris.fr/explore/dataset/sites-disposant-du-service-paris-wi-fi/export/?disjunctive.cp&disjunctive.etat2).

