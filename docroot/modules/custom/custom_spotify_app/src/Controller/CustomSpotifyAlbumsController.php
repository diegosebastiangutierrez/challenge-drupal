<?php

namespace Drupal\custom_spotify_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomSpotifyAlbumsController extends ControllerBase {

  /**
   * Returns the Spotify Artist page.
   */
  public function list() {
    return new Response('Hello, Spotify Albums!');
  }

}

