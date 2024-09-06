CloudArch - Plateforme de gestion de fichiers clients sécurisée
==============================================================

<p align="center">
  <img src="https://github.com/ipssiwayans/CloudArch/raw/develop/public/images/light_logo.png" alt="Logo CloudArch" width="200">
</p>


Introduction
------------

CloudArch est une plateforme de gestion de fichiers clients sécurisée, conçue pour répondre aux besoins des cabinets
d'architectes. Elle permet aux clients de s'inscrire, de se connecter et de gérer leurs fichiers en toute simplicité,
tout en offrant aux administrateurs un tableau de bord complet pour gérer les utilisateurs et les fichiers.

Fonctionnalités
--------------

* Inscription et connexion sécurisées pour les clients
* Espace de stockage client avec visualisation, téléchargement et suppression de fichiers
* Abonnement unique avec possibilité d'achat d'espace supplémentaire
* Tableau de bord administrateur pour gérer les utilisateurs et les fichiers
* Notifications par e-mail pour les confirmations d'inscription, de suppression de compte et d'achat d'espace
  supplémentaire

Technologies utilisées
-----------------------

* Symfony pour le développement backend
* Twig pour le développement frontend
* Bootstrap pour le design responsive

Installation
------------

1. Cloner le dépôt GitHub
2. Installer les dépendances`composer install` à la racine du projet
3. Demander un fichier `.env` à l'équipe de développement
4. Exécuter les migrations`php bin/console doctrine:migrations:migrate`
5. Lancer l'application à l'aide de la commande `php bin/console server:start`
6. Tout est prêt !

Contribution
------------

Toutes les contributions sont les bienvenues ! Si vous souhaitez contribuer à CloudArch, veuillez soumettre une pull
request sur la branche `develop`.

Licence
-------

CloudArch est sous licence MIT

Contact
-------

Si vous avez des questions ou des commentaires sur CloudArch, n'hésitez pas à nous contacter
à [support@cloudarch.com](mailto:support@cloudarch.com).
