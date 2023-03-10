<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;

use Drupal\custom_spotify_entities\Entity\Artist;
use Drupal\custom_spotify_entities\Entity\Album;
use Drupal\custom_spotify_entities\Entity\Song;

use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;


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
      '#submit' => ['::submitForm','::crawl_new_info'],
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

  /**
   * Custom function to scrap and update artist, album and song information.
   */
  public function crawl_new_info() {

    $config = $this->config('custom_spotify_app.settings');
    // Retrieve the API credentials from the module settings.
    $client_id = \Drupal::config('custom_spotify_app.settings')->get('spotify_client_id');
    $client_secret = \Drupal::config('custom_spotify_app.settings')->get('spotify_client_secret');

    // Initialize the Spotify API client.
    $api = new SpotifyWebAPI();
    $session = new Session($client_id, $client_secret);
    $session->requestCredentialsToken();
    $accessToken = $session->getAccessToken();

    $api->setAccessToken($session->getAccessToken());

    // Retrieve a list of new releases from the Spotify API.
    $contents = $api->getNewReleases();

    //Set list of Albums
    $albums = $contents->albums->items;

    foreach ($albums as $album) {

      //References to add to the node
      $album_artists = array();

      //Check if album exists, if not, create it
      error_log("Album Exists?: " . $album->id);
      error_log($this->checkAlbum($album->id));

      if(!$this->checkAlbum($album->id)){

        $album_node = $api->getAlbum($album->id);
        //Saving genres to add to the node
        $album_genres = array();

        foreach($album_node->genres as $genre){
          $album_genres[] = array('target_id' => $this->checkGenreAndGenerate($genre));
        }

        foreach($album_node->artists as $artist){
          if($aid = $this->checkArtist($artist->id)) $album_artists[] = $aid;
          else{
            //Create Artist from API Call
            $album_artists[] = array('target_id' => $this->createArtist($artist->id, $api));
          }
        }

        // Create the album entity.
        $album_entity = \Drupal::entityTypeManager()->getStorage('album')->create([
          'title' => $album_node->name,
          'spotify_id' => $album_node->id,
          'spotify_detail_url' => $album_node->external_urls->spotify,
          'genre' => $album_genres,
          'popularity' => $album_node->popularity,
          'release_date' => $album_node->release_date,
          'artist' => $album_artists,
          'cover_image' => [
            'uri' => $album_node->images[0]->url,
            'width' => $album_node->images[0]->width,
            'height' => $album_node->images[0]->height,
            'alt' => $album_node->name,
            'title' => $album_node->name,
          ],
        ]);
        $album_entity->save();

        //Retrieving Album Tracks
        foreach($album_node->tracks->items as $track){
          //References to add to the node
          $track_artists = array();

          foreach($track->artists as $artist){
            if($aid = $this->checkArtist($artist->id)) $track_artists[] = $aid;
            else{
              //Create Artist from API Call
              $track_artists[] = array('target_id' => $this->createArtist($artist->id, $api));
            }
          }

          $song_entity = \Drupal::entityTypeManager()->getStorage('song')->create([
            'title' => $track->name,
            'spotify_id' => $track->id,
            'spotify_detail_url' => $track->external_urls->spotify,
            'artist' => $track_artists,
            'album' => $album_node->name,
            'disc_number' => $track->disc_number,
            'track_number' => $track->track_number,
            'duration_ms' => $track->duration_ms,
            'preview_url' => $track->preview_url,
          ]);
          $song_entity->save();
        }
      }
    }
  }

  public function createAlbum($albumId, SpotifyWebAPI &$api){

    $artist_node = $api->getAlbum($albumId);
    $artist_genres = array();

    foreach($artist_node->genres as $genre){
      $artist_genres[] = array('target_id' => $this->checkGenreAndGenerate($genre));
    }

    // Create the artist entity.
    $artist_entity = \Drupal::entityTypeManager()->getStorage('artist')->create([
      'name' => $artist_node->name,
      'spotify_id' => $artist_node->id,
      'spotify_detail_url' => $artist_node->external_urls->spotify,
      'genre' => $artist_genres,
      'popularity' => $artist_node->popularity,
      'followers' => $artist_node->followers->total,
      'cover_image' => [
        'uri' => $artist_node->images[0]->url,
        'width' => $artist_node->images[0]->width,
        'height' => $artist_node->images[0]->height,
        'alt' => $artist_node->name,
        'title' => $artist_node->name,
      ],
    ]);
    $artist_entity->save();
    return $this->checkAlbum($artist_node->id);
  }

  public function createArtist($artistId, SpotifyWebAPI &$api){

    $artist_node = $api->getArtist($artistId);
    $artist_genres = array();

    foreach($artist_node->genres as $genre){
      $artist_genres[] = array('target_id' => $this->checkGenreAndGenerate($genre));
    }

    // Create the artist entity.
    $artist_entity = \Drupal::entityTypeManager()->getStorage('artist')->create([
      'name' => $artist_node->name,
      'spotify_id' => $artist_node->id,
      'spotify_detail_url' => $artist_node->external_urls->spotify,
      'genre' => $artist_genres,
      'popularity' => $artist_node->popularity,
      'followers' => $artist_node->followers->total,
      'cover_image' => [
        'uri' => $artist_node->images[0]->url,
        'width' => $artist_node->images[0]->width,
        'height' => $artist_node->images[0]->height,
        'alt' => $artist_node->name,
        'title' => $artist_node->name,
      ],
    ]);
    $artist_entity->save();
    return $this->checkArtist($artist_node->id);
  }

  public function checkArtist($id){
    // Check if the artist entity already exists.
    $artist_entity = \Drupal::entityTypeManager()->getStorage('artist')->load($id);
    if (!$artist_entity) return FALSE;
    return $artist_entity;

  }

  public function checkAlbum($id){
    // Check if the artist entity already exists.
    $album_entity = \Drupal::entityTypeManager()->getStorage('album')->load($id);
    if (!$album_entity) return FALSE;
    return $album_entity;
  }

  public function checkSong($id){
    // Check if the artist entity already exists.
    $song_entity = \Drupal::entityTypeManager()->getStorage('artist')->load($id);
    if (!$song_entity) return FALSE;
    return $song_entity;
  }

  public function checkGenreAndGenerate($genre_name){

    // Look up or create the genre term.
    $genre_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $genre_name, 'vid' => 'genre']);

    if (!$genre_term) {
      $genre_term = Term::create([
        'name' => $genre_name,
        'vid' => 'genre',
      ]);
      $genre_term->save();
      $genre_term = reset($genre_term);
    }
    return $genre_term->id;

  }
}
