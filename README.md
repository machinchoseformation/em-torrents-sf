#Torrent Parser | SF2
###Objectifs :
- Automatiser la recherche de nouveaux torrents (movies) de qualité
- Récupérer des infos sur les films
- Créer un affichage personnalisé des films
- Gérer les films déjà téléchargés, ignorés, déjà vus, etc.
- Envoyer des alertes par email (ou sms) lors de la découverte de nouveaux films

###Infos à parser et à sauvegarder à propos du torrent :
- nom du torrent
- lien "magnet"
- info hash
- nombre de seeders
- nombre de leetchers
- qualité (ts, brrip, dvdrip, cam, etc...)

###Infos à parser et à sauvegarder à propos du film :
- titre
- id imdb
- année
- réalisateur du film
- url du poster principal (au moins 200px de large)
- rating
- nombre de votes

##Cli :
La commande a réaliser doit permettre de récupérer les nouveaux torrents de films en fonction de plusieurs critères. Ce code est placé dans une commande pour être très facilement exécutable automatiquement, à intervalle régulier (cron job).
Voici les grandes étapes pour récupérer les données :
1. Au lancement de la commande, parser la page suivante :
http://kickass.to/movies/?field=seeders&sorder=desc
2. Récupérer tous les liens présents vers les pages de détail de torrent
3. Parser chacune des pages de détails pour obtenir toutes les infos requises sur le torrent.
4. Récupérer l'url imdb, présente sur cette page de détails
5. Parser cette page imdb pour récupérer toutes les infos requises sur le film.

Une fois toutes ces infos récupérées, sauvegarder-les en bases. Faites les vérifications nécessaires avant de décider de sauvegarder (film/torrent déjà présent ? torrent de qualité suffisante ? Rating imdb assez intéressant ? etc. ) et envoyer un email contenant les infos des nouveaux films (peut-être fait plus tard).

Conseil : idéalement, déplacer le plus de code possible de la Command vers un ou des Service, afin de garder la commande la plus light possible, et de pouvoir éventuellement appeler le code autrement que par la commande (API Rest par exemple).

##Application Web :
L'application web vous permet d'afficher et de gérer les données récupérées par la commande, et de lancer manuellement les téléchargements.
La page principale affiche la liste des torrents de votre bdd, du plus récent au plus ancien. Chaque torrent est représenté (principalement en tout cas) par le poster du film.
Pour chaque torrent, il est possible de réaliser les actions suivantes :
- Voir plus de détails sur le torrent/film
- Lancer le téléchargement (lien magnet)
- Ne plus proposer ce torrent
- Ne plus proposer ce film (mauvais film)
- Ne plus proposer ce film (déjà vu)
- etc.

Les détails du torrent/film s'affichent dans la barre latérale (une zone toujours visible, réservée à cet effet), et non dans une autre page (ajax requis).
Les autres actions doivent marquer visuellement le torrent comme tel, soit en le faisant disparaitre, soit en inscrivant son statut par-dessus le poster, comme vous voulez (ajax très souhaitable).

Il devrait toutefois être possible de n'afficher que les torrents/films étant placé sous un certain statut (voir tous les films refusés par exemple) (ajax à peine recommandé).
