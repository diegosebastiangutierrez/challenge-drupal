<?php

namespace Drupal\custom_spotify_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class CustomSpotifyAppController extends ControllerBase {

  /**
   * Returns the Spotify Artist page.
   */
  public function artistPage() {
    return new Response('Hello, Spotify Artist!');
  }

  /**
   * Returns the Spotify Artist module settings form.
   */
  public function settingsForm() {
    $form = [
      '#type' => 'markup',
      '#markup' => $this->t('Configure the Spotify Artist module settings here.'),
    ];

    return $form;
  }

}

