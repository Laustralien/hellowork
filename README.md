## Lancement 
- lancement du projet via > composer install && symfony server:start

## Fonctions disponibles
- page d'accueil avec les différentes pages /
- importer les offres pole-emploi de Bordeaux/Rennes/(code insee Paris ? ) /import/33063/<Bearer>
- ajout manuel d'offre /new
- liste des offres sauvegardées /list
- ajout d'une offre en dure (depuis l'url /test)

## Précisions
- l'import ne peut se faire actuellement qu'en ajoutant en Bearer (récupérable depuis l'interface Swagger)
- je n'ai pas réussi a faire fonctionner la récupération auto du bearer avec le client_api et client_secret
- je n'ai pas fait le rapport statistique (mise a part si la liste des offres convient).

## Difficultés
- mise en place d'un environnement fonctionnel; avec certaines conf de mon système.
- récupération du jeton d'authentification avec l'API disponible

## Axes d'amélioration :
- Optimisations et ajout d'option pour l'import complet ou partiel des offres
- Tests unitaires
- Gestion plus fine des erreurs 



Si besoin possibilité de trouver un client non symfony avec  https://editor.swagger.io/ + le swagger.json en annexe