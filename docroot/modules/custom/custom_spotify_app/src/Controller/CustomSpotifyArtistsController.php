<?php

namespace Drupal\custom_spotify_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomSpotifyArtistsController extends ControllerBase {

  /**
   * Returns the Spotify Artist list page.
   */
  public function list() {
    return new Response('Hello, Spotify Artist List!');
  }

  /**
   * Returns the Spotify Artist page.
   */
  public function detail() {
    return new Response('Hello, Spotify Artist Detail!');
  }

}
