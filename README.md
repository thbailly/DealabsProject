## Installation

### Clonage du repos
> git clone https://github.com/thbailly/DealabsProject.git

### Installation et initialisation

>cd bailly-barland/DealabsProject  

>make init

Il faut maintenant éditer le fichier **.env.local** et remplacer **IPARENSEIGNER** (ligne **24** et **32**) par la bonne adresse IP (192.168.....) 

Ensuite on lance les conteneurs docker :

> make start

Puis l'installation des dépendances :

> make install

*Si erreur "SQLSTATE[HY000] [2002] Connection refused", bien vérifier l'IP dans le **.env.local** et relancer la commande `make install`.*

Les différents éléments sont maintenant accessibles via les URL suivantes : 

*  Apache : `http://localhost:9521`
*  PhpMyAdmin : `http://localhost:9524`
*  Mailcatcher : `http://localhost:9522`

## Docker

* Lancement :  `make start`
* Arrêt : `make stop`

## Identifiants

* Mysql :bailly_barland:bailly_barland
* Apache: email :test@test.com  Username : test Password : test

## API

L'API publique se trouve à l'URL suivant : 

     http://localhost:9521/api/weeklyDeals

Celle-ci retourne les titres des deals de la semaine.

L'API avec authentification se trouve à l'URL suivante :

    http://localhost:9521/api/users/2/savedDeals

L'authentification se fait via un token. Pour récupérer ce token, il suffit de lancer la commande suivante : 

> make getApiToken

Ce qui donne par exemple : 

    Bearer f8acc1e3899d93cc90a14179bf80e927a8fe68debe4472cf6a5692b00496aba58ecdfa728870d3fadbb1a4cb12e618013ea7b40a5221d662fd9aa976

A la racine du repo se trouve le dossier PostmanCollection avec à l'intérieur une collection à importer dans postman afin de tester directement les API.

Afin de tester l'API avec authentification, il suffit d'utiliser une requête avec dans le header une Authorization de type "API key" avec comme key : "Authorization" et en value le token récupérer via la commande précédente (Bearer .....)
