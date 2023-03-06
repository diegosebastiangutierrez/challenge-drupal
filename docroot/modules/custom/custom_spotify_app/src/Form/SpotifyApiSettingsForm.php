<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use  SpotifyWebAPI\SpotifyWebAPI;

/**
 * Class SpotifyApiSettingsForm.
 *
 * @package Drupal\custom_spotify_app\Form
 */
class SpotifyApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['custom_spotify_app.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the current configuration values.
    $config = $this->config('custom_spotify_app.settings');

    // Add a fieldset to the form for the Spotify API credentials.
    $form['spotify_api_credentials'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Spotify API credentials'),
    ];

    // Add the Spotify API Client ID field.
    $form['spotify_api_credentials']['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('Enter your Spotify App Client ID.'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE,
    ];

    // Add the Spotify API Client Secret field.
    $form['spotify_api_credentials']['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Enter your Spotify App Client Secret.'),
      '#default_value' => $config->get('client_secret'),
      '#required' => TRUE,
    ];

    // Add the Spotify API access token field.
    $form['spotify_api_credentials']['access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#description' => $this->t('Your Spotify App Access Token.'),
      '#default_value' => $config->get('access_token'),
      '#required' => FALSE,
    ];

    // Add the Spotify API token created at field.
    $form['spotify_api_credentials']['spotify_access_token_created_at'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token Creation Time'),
      '#description' => $this->t('Your Spotify App Access Token Creation Time.'),
      '#default_value' => $config->get('spotify_access_token_created_at'),
      '#required' => FALSE,
    ];
    // Add the Spotify API Access Token Expiration field.
    $form['spotify_api_credentials']['spotify_access_token_expiration_time'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token Expiration'),
      '#description' => $this->t('Your Spotify App Access Token Expiration Time.'),
      '#default_value' => $config->get('spotify_access_token_expiration_time'),
      '#required' => FALSE,
    ];

    // Add a fieldset to the form for the caching settings.
    $form['caching_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Caching settings'),
    ];

    // Add the caching time field.
    $form['caching_settings']['caching_time'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Caching time'),
      '#description' => $this->t('Enter the caching time in seconds.'),
      '#default_value' => $config->get('caching_time'),
      '#required' => TRUE,
    ];

    // Add a submit button to save the form values.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    // Add a submit button to save the form values.
    $form['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import New Content'),
      '#submit' => array('::crawl_new_info'),
    ];

    return $form;
  }

    /**
   * Custom function to scrap and update artist, album and song information.
   */
  public function crawl_new_info(array &$form, FormStateInterface $form_state) {
    // Retrieve the API credentials from the module settings.
    $client_id = \Drupal::config('custom_spotify_app.settings')->get('client_id');
    $client_secret = \Drupal::config('custom_spotify_app.settings')->get('client_secret');

    // Initialize the Spotify API client.
    $api = new \SpotifyWebAPI\SpotifyWebAPI();
    $session = new \SpotifyWebAPI\Session($client_id, $client_secret);
    $api->setAccessToken($session->getAccessToken());

    // Define the number of artists to retrieve.
    $artist_limit = 20;

    // Retrieve a list of artists from the Spotify API.
    $artists = $api->search('artist', 'spotify', ['limit' => $artist_limit])->artists->items;

    // Loop through each artist and create an "Artist" node.
    foreach ($artists as $artist) {
      $node = \Drupal::entityTypeManager()->getStorage('node')->create([
        'type' => 'artist',
        'title' => $artist->name,
      ]);
      $node->set('field_artist_id', $artist->id);
      $node->set('field_artist_image', $artist->images[0]->url);
      $node->set('field_artist_genres', $artist->genres);
      $node->set('field_artist_popularity', $artist->popularity);
      $node->save();

      // Retrieve the albums for the current artist.
      $albums = $api->getArtistAlbums($artist->id, ['limit' => 50])->items;

      // Loop through each album and create an "Album" node.
      foreach ($albums as $album) {
        $album_node = \Drupal::entityTypeManager()->getStorage('node')->create([
          'type' => 'album',
          'title' => $album->name,
        ]);
        $album_node->set('field_album_id', $album->id);
        $album_node->set('field_album_artist', $node->id());
        $album_node->set('field_album_image', $album->images[0]->url);
        $album_node->set('field_album_release_date', $album->release_date);
        $album_node->save();

        // Retrieve the tracks for the current album.
        $albumTracks = $this->spotifyService->getAlbumTracks($album['id']);

        // Loop through the tracks and create a Song node for each one.
        foreach ($albumTracks['items'] as $song) {
          $song_node = $this->nodeStorage->create([
            'type' => 'song',
            'title' => $song['name'],
            'field_song_duration' => $song['duration_ms'],
            'field_song_preview_url' => $song['preview_url'],
            'field_song_album' => $album_node,
          ]);

          // Save the Song node.
          $song_node->save();
        }
      }
    }
  }

}
