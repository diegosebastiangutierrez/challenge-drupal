<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\custom_spotify_entities\Entity\Artist;
use Drupal\custom_spotify_entities\Entity\Album;
use Drupal\custom_spotify_entities\Entity\Song;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;


/**
 * Class SpotifyApiSettingsForm.
 *
 * @package Drupal\custom_spotify_app\Form
 */
class SpotifyApiSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'custom_spotify_app.settings';

  protected $SpotifyService;

  /**
   * Class Constructor
   */

  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('custom_spotify_app.api_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the current configuration values.
    $config = $this->config(static::SETTINGS);

    // Add a fieldset to the form for the Spotify API credentials.
    $form['spotify_api_credentials'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Spotify API credentials'),
    ];

    // Add the Spotify API Client ID field.
    $form['spotify_api_credentials']['spotify_client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('Enter your Spotify App Client ID.'),
      '#default_value' => $config->get('spotify_client_id'),
      '#required' => TRUE,
    ];

    // Add the Spotify API Client Secret field.
    $form['spotify_api_credentials']['spotify_client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Enter your Spotify App Client Secret.'),
      '#default_value' => $config->get('spotify_client_secret'),
      '#required' => TRUE,
    ];

    // Add the Spotify API access token field.
    $form['spotify_api_credentials']['spotify_access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#description' => $this->t('Your Spotify App Access Token.'),
      '#default_value' => $config->get('spotify_access_token'),
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

    // Add a fieldset to the form for the Spotify API credentials.
    $form['spotify_api_limits'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Spotify API Limits'),
    ];

    // Add the Spotify API Query Limit field.
    $form['spotify_api_limits']['spotify_api_query_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('API Query Limit'),
      '#description' => $this->t('Your Spotify API Query Limit.'),
      '#default_value' => $config->get('spotify_api_query_limit'),
      '#min' => 0,
      '#max' => 50,
      '#required' => FALSE,
    ];

    // Add the Spotify API Query Offset field.
    $form['spotify_api_limits']['spotify_api_query_offset'] = [
      '#type' => 'number',
      '#title' => $this->t('API Query Offset'),
      '#description' => $this->t('Your Spotify API Query Offset.'),
      '#default_value' => $config->get('spotify_api_query_offset'),
      '#min' => 0,
      '#max' => 50,
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
    $form['actions']['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import New Content'),
      '#submit' => ['::custom_spotify_app_create_entities_from_spotify_api'],
      '#weight' => 10,
    ];

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      ->set('spotify_access_token', $form_state->getValue('spotify_access_token'))
      ->set('spotify_client_id', $form_state->getValue('spotify_client_id'))
      ->set('spotify_client_secret', $form_state->getValue('spotify_client_secret'))
      ->set('spotify_access_token', $form_state->getValue('spotify_access_token'))
      ->set('spotify_access_token_created_at', $form_state->getValue('spotify_access_token_created_at'))
      ->set('spotify_access_token_expiration_time', $form_state->getValue('spotify_access_token_expiration_time'))
      ->set('spotify_api_query_limit', $form_state->getValue('spotify_api_query_limit'))
      ->set('spotify_api_query_offset', $form_state->getValue('spotify_api_query_offset'))
      ->set('caching_time', $form_state->getValue('caching_time'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->getValue('spotify_access_token')) {
      $service = \Drupal::service('custom_spotify_app.api_service')->auth();
      $form_state->setValue('spotify_access_token', $service->access_token);
      $form_state->setRebuild();
    }
  }

  public function custom_spotify_app_create_entities_from_spotify_api() {

    //$config = $this->config('custom_spotify_app.settings');
    // Retrieve the API credentials from the module settings.
    $client_id = \Drupal::config('custom_spotify_app.settings')->get('spotify_client_id');
    $client_secret = \Drupal::config('custom_spotify_app.settings')->get('spotify_client_secret');

    // Initialize the Spotify API client.
    $api = new SpotifyWebAPI();
    $session = new Session($client_id, $client_secret);
    $session->requestCredentialsToken();
    //$accessToken = $session->getAccessToken();

    $api->setAccessToken($session->getAccessToken());

    // Obtain a list of new releases from the Spotify API.
    $artists_data = $api->getArtists('3fMbdgg4jU18AjLCKBhRSm,2UZIAOlrnyZmyzt1nuXr9y,6tbjWDEIzxoDsBA1FuhfPW,5lpH0xAS4fVfLkACg9DAuM,4RVnAU35WRWra6OZ3CbbMA,1zuJe6b1roixEKMOtyrEak,5rSXSAkZ67PYJSvpUpkOr7,6Ff53KvcvAj5U7Z1vojB5o,26dSoYclwsYLMAKD3tpOr4,5pKCCKE2ajJHZ9KAiaK11H,0Ty63ceoRnnJKVEYP0VQpk,762310PdDnwsDxAQxzQkfX,1eClJfHLoDI4rZe5HxzBFv,1w5Kfo2jwwIPruYS2UWh56,4Z8W4fKeB5YxbusRsdQVPb');


    foreach ($artists_data->artists as $artist) {

      // Check if the artist entity already exists.
      $artist_entity = \Drupal::entityTypeManager()
      ->getStorage('artist')
      ->loadByProperties(['spotify_id' => $artist->id]);

      // If artist is not on database we create it
      if (!$artist_entity) {

        // Add the genre terms to the artist.
        $artist_genre_terms = [];

        foreach ($artist->genres as $genre_name) {
          // Look up or create the genre term.
          $artist_genre_term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')->loadByProperties(['vid' => 'genres', 'name' => $genre_name]);

          if (!$artist_genre_term) {
            $artist_genre_term = Term::create([
              'name' => $genre_name,
              'vid' => 'genres',
            ]);
            $artist_genre_term->save();
          }
          $artist_genre_terms[] = $artist_genre_term;
        }

        // Create the artist entity.
        $artist_entity = Artist::create([
          'name' => $artist->name,
          'spotify_id' => $artist->id,
          'spotify_detail_url' => $artist->external_urls->spotify,
          'popularity' => $artist->popularity,
          'followers' => $artist->followers->total,
          'genre' => $artist_genre_terms,
          'cover_image' => [
            'uri' => $artist->images[0]->url,
            'width' => $artist->images[0]->width,
            'height' => $artist->images[0]->height,
            'alt' => $artist->name,
            'title' => $artist->name,
          ],
        ]);
        $artist_entity->save();
      }

      // Now we get the artist albums
      $artist_albums = $api->getArtistAlbums($artist->id);

      foreach ($artist_albums->items as $album) {

        $album_entity = \Drupal::entityTypeManager()
        ->getStorage('album')
        ->loadByProperties(['spotify_id' => $album->id]);

        if (!$album_entity) {
          // Add the genre terms to the album.
          $album_genre_terms = [];

          foreach ($album->genres as $genre_name) {
            // Look up or create the genre term.
            $album_genre_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['vid' => 'genres', 'name' => $genre_name]);

            if (!$album_genre_term) {
              $album_genre_term = Term::create([
                'name' => $genre_name,
                'vid' => 'genres',
              ]);
              $album_genre_term->save();
            }
            $album_genre_terms[] = $album_genre_term;
          }
          // Create the album entity.
          $album_entity = Album::create([
            'spotify_id' => $album->id,
            'spotify_detail_url' => $album->external_urls->spotify,
            'title' => $album->name,
            'genre' => $album_genre_terms,
            //'artist' => ['target_id' => $artist_entity->id],
            'popularity' => $album->popularity,
            'release_date' => $album->release_date,
            'cover_image' => [
              'uri' => $album->images[0]->url,
              'width' => $album->images[0]->width,
              'height' => $album->images[0]->height,
              'alt' => $album->name,
              'title' => $album->name,
            ],
          ]);
          $album_entity->save();
        }
      }
    }
    /*
      // Loop through the tracks in the album and create a song entity for each track.
      foreach ($album_data->tracks->items as $song_data) {

        $song_entity = \Drupal::entityTypeManager()
          ->getStorage('song')
          ->loadByProperties(['spotify_id' => $song_data->id]);

        if (!$song_entity) {
          // Add the genre term to the song.
          $genre_terms = [];
          foreach ($song_data->genres as $genre_name) {
            // Look up or create the genre term.
            $genre_term = \Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')
              ->loadByProperties(['vid' => 'genres', 'name' => $genre_name]);

            if (!$genre_term) {
              $genre_term = Term::create([
                'name' => $genre_name,
                'vid' => 'genre',
              ]);
              $genre_term->save();
            }
            array_push($genre_terms, ['target_id' => $genre_term->id()]);
          }

          $song_entity = Song::create([
            'spotify_id' => $song_data->id,
            'title' => $song_data->name,
            'spotify_detail_url' => $song_data->external_urls->spotify,
            'artist' => $artist_data,
            'disc_number' => $song_data->disc_number,
            'track_number' => $song_data->track_number,
            'duration_ms' => $song_data->duration_ms,
            'preview_url' => $song_data->preview_url,
            'album' => $album_data,
            'genre' => $genre_terms,
          ]);
          $song_entity->save();
        }
      }
*/
  }
}
