<?php

namespace Drupal\custom_spotify_app\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

/**
 * Provides a 'Top Songs' block.
 *
 * @Block(
 *   id = "custom_spotify_app_top_songs_block",
 *   admin_label = @Translation("Top Songs"),
 * )
 */
class TopSongsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Spotify Web API service.
   *
   * @var \SpotifyWebAPI\SpotifyWebAPI
   */
  protected $spotifyWebApi;

  /**
   * The cache backend.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Constructs a new TopSongsBlock object.
   *
   * @param array $configuration
   *   The block configuration.
   * @param string $plugin_id
   *   The plugin ID for the block.
   * @param mixed $plugin_definition
   *   The plugin definition for the block.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \SpotifyWebAPI\SpotifyWebAPI $spotify_web_api
   *   The Spotify Web API service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CacheBackendInterface $cache_backend, SpotifyWebAPI $spotify_web_api) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->cacheBackend = $cache_backend;
    $this->spotifyWebApi = $spotify_web_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cache.default'),
      $container->get('custom_spotify_app.spotify_web_api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $cache_key = 'custom_spotify_app_top_songs_block';

    if ($cache = $this->cacheBackend->get($cache_key)) {
      $data = $cache->data;
    }
    else {
      $data = $this->getTopSongs();
      $this->cacheBackend->set($cache_key, $data, Cache::EXPIRES_IN, $this->getConfiguration()['cache_time'] * 60);
    }

    $songs = [];

    foreach ($data as $track) {
      $songs[] = [
        'name' => $track->name,
        'artist' => $track->artists[0]->name,
        'album' => $track->album->name,
        'image' => $track->album->images[0]->url,
        'preview_url' => $track->preview_url,
      ];
    }

    return [
      '#theme' => 'custom_spotify_app_top_songs_block',
      '#songs' => $songs,
    ];
  }

  /**
   * Gets the top songs from Spotify.
   *
   * @return array
   *   An array of Spotify track objects.
   */
  protected function getTopSongs() {
    $options
