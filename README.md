# Links Scanner

## Description

Links Scanner est un plugin WordPress conçu pour analyser les liens internes d'une page. 


## Fonctionnalités principales

- Liste tous les liens internes d'une page.

- Affiche le texte d'ancrage associé à chaque lien.

- Identifie les liens internes.

- Vérifie si les liens internes sont cassés.

- Compte le nombre total de liens internes par page.

## Fonctionnement et choix

### Raisonnement 

Etapes :

1 - Récupérer tous les liens de la page grâce à DOMDocument

2 - Vérifier si les liens correspondent à un permalink existant sur le site.

3 - Ajouter tableau des résultats au DOM.

Dans ce code, j'ai opté pour une architecture modulaire et lisible, en séparant les différentes responsabilités du plugin dans des fonctions distinctes. Par exemple, la fonction scan_homepage_links se concentre uniquement sur l'extraction des liens depuis la page d'accueil, tandis que test_url_status gère la vérification de l'état des URL. Cette approche facilite la maintenance et les modifications futures. J'ai également pris en compte l'importance de la sécurité des appels AJAX avec l'utilisation de nonces pour valider les requêtes. Enfin, j'ai structuré les actions WordPress, telles que l'ajout de menus et l'enregistrement des hooks, de manière claire, tout en cherchant à garantir une bonne performance en vérifiant les erreurs et en validant les liens avant de les traiter. Cette organisation vise à garantir une expérience utilisateur fluide, sécurisée et facilement extensible.

## Améliorations possibles

- Choix de la page à scanner.

- Fichier CSV à télécharger.

- Position des liens sur la page (ex. dans le header, body, footer).

- Liste des pages pointées plusieurs fois depuis la même page.

- Identification des pages orphelines (pages sans lien retour).

- Profondeur de lien : distance en clics par rapport à la page d’accueil.





