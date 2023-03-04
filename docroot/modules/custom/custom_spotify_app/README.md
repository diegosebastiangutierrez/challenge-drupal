# Custom Spotify App

Custom Spotify App is a Drupal 9 module that integrates with the Spotify API to display artist information. Once installed, the module provides a page at `/artists` that displays a list of artists fetched from the Spotify API.

## Installation

1. Download and extract the module files to your Drupal 9 installation's `modules/custom` directory.
2. In the command line, navigate to your Drupal 9 installation's root directory and run the following command to install the module:

drush en custom_spotify_app


3. Once the module is installed, navigate to the configuration page at `/admin/config/services/custom-spotify-app` and enter your Spotify API client ID and client secret.

## Usage

1. Navigate to the `/artists` page on your Drupal 9 site to view a list of artists fetched from the Spotify API.
2. Click on an artist's name to view more information about the artist, including their top tracks, albums, and related artists.

## Notes

- This module requires the `jwilsson/spotify-web-api-php` library, which is installed automatically when you enable the module or run `composer install` in the module's directory.
- This module currently only supports fetching artist information from the Spotify API. Additional functionality may be added in future versions.
- This module uses caching to reduce the number of API requests made to the Spotify API. Cached data is stored in Drupal's cache system and is automatically updated every 24 hours.


