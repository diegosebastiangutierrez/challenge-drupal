<?php

namespace Drupal\custom_spotify_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomSpotifyArtistController extends ControllerBase {

  /**
   * Returns the Spotify Artist page.
   */
  public function artistPage() {
    return new Response('Hello, Spotify Artist!');
  }

}

