![image](https://user-images.githubusercontent.com/108840415/224860496-7ce6c1ab-9b03-4553-8a5d-8da8cecc0191.png)


# Custom Spotify App
Custom Spotify App is a Drupal 9 module that integrates with the Spotify API to display artist information. Once installed, the module provides a page at `/artists` that displays a list of artists fetched from the Spotify API.


## Installation

 Add the following host data to your hosts file:

* 127.0.0.1 challenge-drupal.devel
* 127.0.0.1 mariadb.devel
* 127.0.0.1 pma.devel

These are the development urls for the site, mariadb server and phpmyadmin

- Enter your termimal and run:

`mkdir /var/wwww;cd /var/www;`
`git clone https://github.com/diegosebastiangutierrez/challenge-drupal.git`
`cd challenge-drupal;cd mariadb-init;tar -xvf drupal-initial.tgz;cd ..;`

- This will clone the repo locally (use a /var/www/challenge-drupal folder) and uncompress the initial database for the site.

`docker-compose up -d`

- This will create the docker containers to hold the challenge website.

- Open Docker Desktop and go to the 'challenge-drupal' container group, select the 'challenge-drupal_php' container and open a terminal.
- Now, enter the 'docroot' folder. Run the following commands:

* composer install
* composer diagnose

- Go to your browser and type 'http://challenge-drupal.devel'

## ATTENTION: WE ARE USING DRUPAL 9 WITH PHP 7.4.3.

- We used php-fpm 7.4.3 for the PHP container, because the 'jwilsson/spotify-web-api-php' library needed to use the spotify services does not work with php 8. No problems found (for now).

## THE CHALLENGE WEBSITE IS INSTALLED!

Now, we proceed with the following commands on the 'docroot' folder:

* drush en custom_spotify_app
* drush cr

And the site is running! Go to your browser and type 'http://challenge-drupal.devel', login with "admin" user and "yugo7fuego" password.

## NEXT: IMPORT DATA

- Logged in as admin, go to http://challenge-drupal.devel/admin/config/services/custom-spotify-app
- Click the "Save Configuration" button.
- Click the "Import New Content" button and wait till the module finishes the import process.

Once it finishes, go to the homepage of the site. START BROWSING!
