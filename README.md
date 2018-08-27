Symfony Edition Standard
========================

Ce projet est basé sur la version 3.4 LTS de symfony

Contenu
--------------
But du projet:

j'ai mis en place une authentification complète allant de l'inscription d'un membre jusqu'à son arrivée sur son espace dédié.



Gestion des assets:

j'ai créé un projet symfony en utilisant webPack Encore pour gérer les assets



Fonctionnalités:

1/ j'ai mis en place une confirmation de création de compte par envoi d'e-mail contenant un lien avec un token de sécurité,l'utilisateur ayant 12 heures de délais pour confirmer son nouveau compte

2/ si l'utilisateur clique sur le lien plus de 12 heures après la réception du 1er e-mail,son token est invalidé,un nouveau token de sécurité est généré et envoyé à l'utilisateur automatiquement
( à partir de ce moment,il a 6 heures pour valider son compte)

3/ si l'utilisateur a perdu son mot de passe ,il peut demander une rénitialisation de son mot de passe pour recevoir un lien avec un token de sécurité



Options:

j'ai également utilisé des eventListeners et Subscribers afin de réduire la quantité de code présent dans les controllers


