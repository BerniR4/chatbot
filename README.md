## Instal·lació

En primer lloc cal crear una nova carpeta sota el directori `/blocks/` de Moodle amb el nom de `chatbot` 
i copiar-hi el contingut d'aquesta carpeta. És important que s'utilitzi aquest nom. Aquest primer pas, de fet, 
és com s'instal·la qualsevol bloc a Moodle.

Seguidament, cal instal·lar les dependències de BotMan. Per a fer-ho, dins de la carpeta `chatbot`, s'ha d'executar el següent:

```sh
$ cd botman
$ composer install
```

Per acabar, només és necessari actualitzar Moodle per tal que detecti el nou bloc.