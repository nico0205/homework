# homework

## Installation


## Permission to write /public/
Pour gérer l'upload des images, l'application doit créer un dossier /media/uploads dans le dossier public.

Le serveur web doit donc avoir les droits d'écriture

En passant par les ACL

```text
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX public

