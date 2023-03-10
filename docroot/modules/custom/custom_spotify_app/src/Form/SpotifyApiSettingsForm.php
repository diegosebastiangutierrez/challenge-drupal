<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\taxonomy\Entity\Term;

use Drupal\custom_spotify_entities\Entity\Artist;
use Drupal\custom_spotify_entities\Entity\Album;
use Drupal\custom_spotify_entities\Entity\Song;

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

    //Obtengo lista de Albums
    $albums = $contents->albums->items;

    foreach ($albums as $album) {

      //References to add to the node
      $album_artists = array();

      //Check if album exists, if not, create it
      if(!$this->checkAlbum($album->id)){

        $album_node = $api->getAlbum($album->id);
        //Saving genres to add to the node
        $album_genres = array();

        foreach($album_node->genres as $genre){
          $album_genres[] = $this->checkGenreAndGenerate($genre);
        }

        foreach($album_node->artists as $artist){
          if($aid = $this->checkArtist($artist->id)) $album_artists[] = $aid;
          else{
            //Create Artist from API Call
            $album_artists[] = $this->createArtist($artist->id, $api);
          }
        }

        $new_album = Album::create([
          'type' => 'album',
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
        $new_album->save();

        //Retrieving Album Tracks
        $album_tracks = $api->getAlbumTracks($album_node->id);

        foreach($album_tracks->items as $track){
          //References to add to the node
          $track_artists = array();

          foreach($track->artists as $artist){
            if($aid = $this->checkArtist($artist->id)) $track_artists[] = $aid;
            else{
              //Create Artist from API Call
              $track_artists[] = $this->createArtist($artist->id, $api);
            }
          }

          $new_track = Song::create([
            'type' => 'song',
            'title' => $track->name,
            'spotify_id' => $track->id,
            'spotify_detail_url' => $track->external_urls->spotify,
            'disc_number' => $track->disc_number,
            'track_number' => $track->track_number,
            'duration_ms' => $track->duration_ms,
            'preview_url' => $track->preview_url,
            'artist' => $track_artists,
          ]);
          $new_track->save();
        }
      }
    }
  }

  public function createArtist($artistId, SpotifyWebAPI &$api){

    $artist_node = $api->getArtist($artistId);
    $artist_genres = array();

    foreach($artist_node->genres as $genre){
      $artist_genres[] = $this->checkGenreAndGenerate($genre);
    }

    $new_artist = Artist::create([
      'type' => 'artist',
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

    $new_artist->save();
    return $new_artist;

  }

  public function checkArtist($id){
    //Check if artist exists
    $query = \Drupal::entityQuery('artist');
    $query->condition('spotify_id', $id);
    $results = $query->execute();
    if(!count($results)) return FALSE;
    else return $results;
  }

  public function checkAlbum($id){
    //Check if artist exists
    $query = \Drupal::entityQuery('album');
    $query->condition('spotify_id', $id);
    $results = $query->execute();
    if(!count($results)) return FALSE;
    else return $results;
  }

  public function checkSong($id){
    //Check if artist exists
    $query = \Drupal::entityQuery('song');
    $query->condition('spotify_id', $id);
    $results = $query->execute();
    if(!count($results)) return FALSE;
    else return $results;
  }

  public function checkGenreAndGenerate($genre){
    //Check if genre exists
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', "genres");
    $query->condition('name', $genre);
    $tids = $query->execute();

    if($tids) return $tids;
    else{
      $term = \Drupal\taxonomy\Entity\Term::create([
        'vid' => 'genres',
        'name' => $genre,
      ]);
      $term->enforceIsNew();
      $term->save();
      return $term;
    }
  }
}
