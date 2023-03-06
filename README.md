# Drupal Challenge

** Ejecutar en la terminal:

'docker-compose up -d'

* Esto iniciará el ambiente y sus containers.
* Se crearán las instancias necesarias para el proyecto en Docker.
* Se levanta la base de datos. Pueden hacer un import de la base de datos inicial, o de la base de datos completa.
En el caso de levantar la base de datos inicial:

- Ingresar en el directorio docroot/
- Ejecutar en terminal:

'drush en custom_spotify_app'

Esto habilita el módulo creado.

## Qué hace el módulo?

* Crea las entidades y tipos de contenido Artist, Album, Song
* Setea los path alias de pathauto para los contents Artist, Album, Song
* Genera un form de configuración para el módulo donde se debe cargar el client_secret y client_id de la Web API de Spotify, y el tiempo de caché para las llamadas.
- Esta config llega con los siguientes valores por defecto:
    * client_id = 1bccca894a2b4c9d80b11b3c063010e0
    * client_secret = a9c5b03611304597a364ca06ba5413a8
    * cache_time = 3600
- 
