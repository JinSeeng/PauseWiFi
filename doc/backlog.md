# Backlog et User Stories - Projet Pause WI-FI

## BACKLOG DU PROJET

### Épique 1 : Infrastructure technique

1. Mise en place de l'environnement de développement
    - Configuration du serveur local de développement
    - Installation des frameworks et bibliothèques nécessaires (PHP, MySQL)
    - Configuration du système de contrôle de version

2. Création de la base de données
    - Création du schéma de base pour la table `wifi_spots`
    - Définition des contraintes et des index
    - Ajout des procédures stockées nécessaires

3. Architecture du site
    - Création du modèle MVC pour l'application
    - Configuration des routes et du système de routage
    - Mise en place des modèles d'accès aux données
    - Création d'une couche sécurisée pour les accès à la base de données

4. Sécurité de l'application
    - Implémentation d'un système de gestion des utilisateurs avec mots de passe cryptés
    - Mise en place des mesures contre les injections SQL
    - Protection contre les attaques XSS et CSRF
    - Gestion des sessions sécurisées

### Épique 2 : Import et gestion des données

5. Interface d'administration sécurisée
    - Développement de `admin/import.php` avec accès restreint
    - Création du formulaire d'importation des données
    - Validation et nettoyage des données importées
    - Gestion des erreurs pendant l'importation

6. Importation des spots Wi-Fi
    - Récupération des données depuis le dataset public
    - Téléversement de fichiers (CSV/JSON) contenant les données des spots Wi-Fi
    - Traitement et normalisation des données
    - Insertion dans la base de données avec validation
    - Journalisation des erreurs d'importation

7. Gestion des utilisateurs administrateurs
    - Interface de création et gestion des comptes administrateurs
    - Définition des niveaux d'accès et permissions
    - Stockage sécurisé des mots de passe (cryptage)
    - Mécanisme de récupération de mot de passe

### Épique 3 : Développement des pages principales

8. Page d'accueil (`index.php`)
    - Design de l'interface utilisateur ergonomique
    - Création du formulaire de recherche rapide
    - Implémentation du filtre par arrondissement
    - Gestion appropriée des méthodes GET/POST

9. Carte interactive (`map.php`)
    - Intégration de Leaflet.js avec gestion des erreurs
    - Affichage des marqueurs pour les spots Wi-Fi
    - Développement des événements au clic sur un spot
    - Optimisation pour différents appareils

10. Page de détail (`spot.php`)
    - Affichage des informations détaillées d'un spot
    - Intégration d'une mini-carte pour visualisation
    - Gestion des erreurs (spot inexistant, données manquantes)

11. Système de notification et gestion d'erreurs
    - Création d'un système unifié de notifications
    - Gestion appropriée des erreurs utilisateur et système
    - Messages d'erreur conviviaux et informatifs
    - Journalisation des erreurs critiques

### Épique 4 : Fonctionnalités de recherche et filtrage

12. Moteur de recherche multi-critères
    - Développement d'un algorithme de recherche avancé
    - Implémentation des filtres dynamiques (ex : arrondissement, nom du spot)
    - Optimisation des requêtes pour les performances
    - Gestion des cas d'erreur et résultats vides

### Épique 5 : Fonctionnalités utilisateur

13. Gestion des favoris
    - Implémentation du système de session pour sauvegarder les favoris
    - Développement de `favoris.php`
    - Fonctionnalités d'ajout/suppression de favoris
    - Synchronisation avec compte utilisateur si connecté

14. Espace utilisateur
    - Système d'inscription/connexion sécurisé
    - Profil utilisateur personnalisable
    - Historique des recherches récentes
    - Gestion des préférences de recherche

15. Responsive design et ergonomie
    - Adaptation de toutes les pages pour mobile (mobile first)
    - Test sur différents appareils et navigateurs
    - Optimisation des performances sur appareils mobiles
    - Évaluation et amélioration de l'expérience utilisateur

### Épique Bonus : Internationalisation, Optimisation & Finalisation

16. Optimisation des performances
    - Minification des ressources (CSS, JS)
    - Mise en cache des données fréquemment utilisées
    - Optimisation des requêtes SQL
    - Réduction des temps de chargement

17. Mise en place du système multilingue
    - Création d'un système de fichiers de langue (FR/EN)
    - Implémentation du sélecteur de langue dans l'interface
    - Gestion des erreurs linguistiques

18. Traduction du contenu
    - Création des fichiers de ressources pour le français
    - Création des fichiers de ressources pour l'anglais
    - Traduction des messages d'erreur et notifications

19. Tests et validation linguistique
    - Tests de l'interface dans les deux langues
    - Vérification de l'affichage correct des caractères spéciaux
    - Contrôle de qualité des textes traduits

20. Tests et débogage
    - Tests fonctionnels de toutes les fonctionnalités
    - Tests de sécurité (injection, XSS, etc.)
    - Correction des bugs identifiés

21. Documentation et déploiement
    - Rédaction de la documentation
    - Création d'un guide d'utilisation/d'emploi
    - Déploiement de l'application (hébergeur/LinkedIn)

---

## USER STORIES

### Épique 1 : Infrastructure technique
- En tant que développeur, je veux avoir un environnement de développement opérationnel afin de pouvoir commencer à coder efficacement.
- En tant que développeur, je veux une base de données correctement structurée pour stocker les spots Wi-Fi afin de pouvoir récupérer les données de manière optimale.
- En tant que développeur, je veux une architecture MVC bien organisée afin de pouvoir maintenir et faire évoluer le code facilement.
- En tant qu'administrateur système, je veux que l'application respecte les bonnes pratiques de sécurité afin de protéger les données et l'intégrité du système.

### Épique 2 : Import et gestion des données
- En tant qu'administrateur, je veux pouvoir me connecter à un espace sécurisé afin d'accéder aux fonctionnalités d'administration.
- En tant qu'administrateur, je veux pouvoir téléverser un fichier de données des spots Wi-Fi afin de mettre à jour la base de données sans intervention technique.
- En tant qu'administrateur, je veux visualiser un résumé des données importées avec notification des erreurs éventuelles afin de vérifier que l'importation s'est bien déroulée.

### Épique 3 : Pages principales
- En tant qu'utilisateur, je veux accéder à une page d'accueil claire et intuitive afin de comprendre rapidement le service proposé.
- En tant qu'utilisateur, je veux effectuer une recherche rapide par arrondissement afin de trouver des spots Wi-Fi qui correspondent à mes besoins immédiats.
- En tant qu'utilisateur, je veux visualiser tous les spots Wi-Fi sur une carte interactive afin de repérer facilement ceux qui sont proches de ma position.
- En tant qu'utilisateur, je veux accéder à une page détaillée pour chaque spot Wi-Fi afin d'obtenir toutes les informations nécessaires.
- En tant qu'utilisateur, je veux recevoir des messages d'erreur clairs et compréhensibles lorsqu'une opération échoue afin de savoir comment résoudre le problème.

### Épique 4 : Recherche et filtrage
- En tant qu'utilisateur, je veux effectuer une recherche avec plusieurs critères combinés (arrondissement, nom du spot) afin de trouver précisément ce que je cherche.
- En tant qu'utilisateur, je veux filtrer les spots Wi-Fi afin de trouver ceux qui sont dans le quartier qui m'intéresse.
- En tant qu'utilisateur, je souhaite obtenir des suggestions de spots WiFi similaires à celui que je consulte actuellement, afin de découvrir d'autres lieux susceptibles de m'intéresser.

### Épique 5 : Fonctionnalités utilisateur
- En tant qu'utilisateur, je veux pouvoir créer un compte sécurisé avec un mot de passe crypté afin de protéger mes informations personnelles.
- En tant qu'utilisateur connecté, je veux pouvoir sauvegarder mes spots Wi-Fi préférés afin de les retrouver facilement lors de ma prochaine visite.
- En tant qu'utilisateur connecté, je veux pouvoir consulter ma liste de spots favoris afin de choisir rapidement parmi mes options préférées.
- En tant qu'utilisateur connecté, je veux pouvoir personnaliser mes préférences de recherche afin d'obtenir des résultats plus pertinents pour moi.
- En tant qu'utilisateur mobile, je veux pouvoir utiliser l'application sur mon smartphone avec une expérience optimisée afin de chercher des spots Wi-Fi lorsque je suis déjà en déplacement.

### Épique Bonus : Internationalisation, Optimisation & Finalisation
- En tant qu'utilisateur, je veux que l'application se charge rapidement et réponde instantanément à mes interactions afin de ne pas perdre de temps.
- En tant qu'utilisateur anglophone, je veux pouvoir consulter le site en anglais afin de comprendre facilement tous les contenus et fonctionnalités.
- En tant qu'utilisateur, je veux pouvoir basculer facilement entre le français et l'anglais afin d'utiliser la langue avec laquelle je suis le plus à l'aise.
- En tant qu'administrateur, je veux pouvoir gérer le contenu dans les deux langues afin d'assurer une expérience cohérente pour tous les utilisateurs, quelle que soit la langue choisie.
- En tant qu'utilisateur mobile, je veux que le sélecteur de langue soit facilement accessible sur petit écran afin de pouvoir changer de langue sans difficulté.
