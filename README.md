# homework

Création d'une API REST avec gestion de la sécurité via token JWT permettant des opérations de CRUD sur les articles.

## Pré-requis

- PHP 7.1

## Installation

```text
composer install
```

### Création de la base de données avec fixtures

Création du schema, chargements des fixtures

```text
composer init-db
```

## Permission d'écriture sur /public/
Pour gérer l'upload des images, l'application doit créer un dossier /media/uploads dans le dossier public.

Le serveur web doit donc avoir les droits d'écriture

En passant par les ACL

```text
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public
```

## Erreur d'authentication sur l'API avec APache
Par défaut Apache semble virer le header Authorization. Ce qui bloque l'authentication via le token JWT

Voici la conf à ajouter dans le vhost pour empêcher ce comportement:
```apacheconfig
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1] 
```

## Utilisation

Doc HTTP disponible : http://%yourDomain%/api
Swagger doc disponible : http://%yourDomain%/docs.json

Les fixtures vont créer deux articles et deux users : 

- root / root (droit admin)
- writer / writer (droit de création d'article seulement)

Les routes accessibles publiquement :

- GET /articles
- GET /article/{id}

Les routes de création d'article ou media sont protégées. Seuls les admin ou writer ont un droit d'écriture

- POST /articles
- POST /media
- PUT /article/{id}
- DELETE /article/{id}

Seuls les admins peuvent créer des users:

- POST /user
- DELETE /users
