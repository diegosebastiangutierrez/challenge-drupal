<?php

/**
 * @file
 * Contains Drupal\dc_spotify\Form\DcSpotifyForm.
 */

namespace Drupal\dc_spotify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DcSpotifyForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'dc_spotify.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_playlist_id';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dc_spotify.settings');

    $form['spotify_playlist_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Spotify Playlist ID'),
      '#description' => $this->t('Set a playlist ID for importing Artists and Songs.'),
      '#default_value' => $config->get('spotify_playlist_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('dc_spotify.settings')
      ->set('spotify_playlist_id', $form_state->getValue('spotify_playlist_id'))
      ->save();
  }
}
