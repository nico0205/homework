# homework

Création d'une API REST avec gestion de la sécurité via token JWT permettant des opérations de CRUD sur les articles.

## Pré-requis

- PHP 7.1
- php7.1-sqlite (pour les tests)

## Installation

```text
composer install
```

Editer le fichier .env à la racine du projet pour configurer les accès à la base de données.

Par défaut la BDD sera sous Postgres, mais il est possible de basculer sur Mysql ou SQLite

```text
DATABASE_URL=pgsql://db_user:db_password@127.0.0.1:5432/db_name
DATABASE_DRIVER=pdo_pgsql
DATABASE_VERSION=9.6
```

### Création de la base de données avec fixtures

Création du schema puis chargements des fixtures.
Ce script peut⁻être utiliser à tout moment pour ré-initialiser la BDD

```text
composer init-db
```

## Permission d'écriture sur /public/
Pour gérer l'upload des images, l'application doit créer un dossier /media/uploads dans le dossier public.

Le serveur web doit donc avoir les droits d'écriture dans le dossier public/

En passant par les ACL (si l'OS le supporte)

```text
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public
```

## Configuration Serveur

### Vous pouvez utiliser le build-in server de symfony
```text
php bin/console server:r
```

### Configuration Apache avec PHP-FPM
```apacheconfig
<VirtualHost *:80>
    ServerName domain.tld
    ServerAlias www.domain.tld

    DocumentRoot /var/www/project/public
    <Directory /var/www/project/public>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>

    # uncomment the following lines if you install assets as symlinks
    # or run into problems when compiling LESS/Sass/CoffeeScript assets
    # <Directory /var/www/project>
    #     Options FollowSymlinks
    # </Directory>

    # optionally disable the RewriteEngine for the asset directories
    # which will allow apache to simply reply with a 404 when files are
    # not found instead of passing the request into the full symfony stack
    <Directory /var/www/project/public/bundles>
        <IfModule mod_rewrite.c>
            RewriteEngine Off
        </IfModule>
    </Directory>
    ErrorLog /var/log/apache2/project_error.log
    CustomLog /var/log/apache2/project_access.log combined
</VirtualHost>
```

### Configuration NGINX avec PHP-FPM

```text
server {
    server_name domain.tld www.domain.tld;
    root /var/www/project/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php7.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}
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

### Se connecter avec le client HTTP

Récupération d'un token via CURL 
```curl
curl -X POST \
  http:///%yourDomain%/login_check \
  -H 'accept: application/ld+json, application/json, text/html' \
  -H 'cache-control: no-cache' \
  -H 'content-type: application/json' \
  -H 'postman-token: e7e0e2d2-8854-6868-aa1a-f8358cf1e0d9' \
  -d '{
	"username": "writer",
	"password": "writer"
}'
```
Copier le token, puis sur le client HTTP

![alt text](doc/authorization1.png "Description goes here")

Coller le token précédé de "Bearer"

![alt text](doc/authorization2.png "Description goes here")

## Tests unitaires et fonctionnels

Les tests sont dans le dossier /tests à la racine du projet

Sont testés, toutes les routes de l'api avec la gestion des authorisations ainsi que le service de téléchargement d'image.

Pour éxécuter les tests

```
php bin/phpunit
```




