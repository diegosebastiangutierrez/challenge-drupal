# Custom Spotify App
Custom Spotify App is a Drupal 9 module that integrates with the Spotify API to display artist information. Once installed, the module provides a page at `/artists` that displays a list of artists fetched from the Spotify API.


## Installation

 Add the following host data to your hosts file:

* 127.0.0.1 challenge-drupal.devel
* 127.0.0.1 mariadb.devel

- Enter your termimal and run:

`mkdir /var/wwww;cd /var/www;`
`git clone https://github.com/diegosebastiangutierrez/challenge-drupal.git`
`cd challenge-drupal`
`docker-compose up -d`

- This will create the docker containers to hold the challenge website

- Open Docker Desktop and go to the 'challenge-drupal' container group, select the 'challenge-drupal_php' container and open a terminal.
- Now, enter the 'docroot' folder. Run the following commands:

* composer install
* composer diagnose
* execute 'mysql -u root -p --host=mariadb.devel', write the 'yugo7fuego' password, then once you get the mysql prompt type: 
* CREATE DATABASE challenge_drupal;exit;
* This will create the database we will use.

- Go to your browser and type 'http://challenge-drupal.devel'

- Install Drupal with a standard profile
- GO TO THE 'docroot/sites/default' folder and copy default.settings.php to settings.php, then 'chmod ugoa+rw settings.php' so the system can write on it.
- The system will ask you for a newer version of PHP. We used php-fpm 7.4.3 for the PHP container, because the 'jwilsson/spotify-web-api-php' library needed to use the spotify services does not work with php 8. Check below the page and click *Continue Anyway*

- Connect your database server. In this case, the hostname is 'mariadb.devel' and the database name is 'challenge_drupal', the username is 'root' and the password is 'yugo7fuego'. You know what follows :D. Click 'Save and Continue'

- Site configuration: 
* Site Name: 'Challenge Drupal Spotify API'
* Email address: you@domain.com
* Site maintenance account: admin
* password: choose a password / password verification
* Default Country: Choose your Contry
* Default Timezone: Choose your Timezonee
* Tick the "Check for updates" box, but not the "Receive email notifications". Then click "Save and continue".

## THE CHALLENGE WEBSITE IS INSTALLED!

Now, we proceed with the following commands on the 'docroot' folder:

* drush en custom_spotify_app

