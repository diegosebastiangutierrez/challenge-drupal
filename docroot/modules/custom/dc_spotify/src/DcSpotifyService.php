<?php

namespace Drupal\dc_spotify;

use DateTime;
use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The DcSpotifyService service class.
 */
class DcSpotifyService {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $http_client;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * Constructor for DcSpotifyService.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle client object.
   *
   * @param \Drupal\key\ConfigFactory $config_factory
   */

  public function __construct(ClientInterface $http_client, ConfigFactory $config_factory) {
    $this->http_client = $http_client;
    $this->config_factory = $config_factory->getEditable('dc_spotify.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('config_factory')
    );
  }

  /**
   * Auth request.
   *
   * @return array
   *  The response body.
   */
  public function auth() {
    $client_id = $this->getClientId();
    $client_secret = $this->getClientSecret();

    $request = $this->http_client->request('POST', 'https://accounts.spotify.com/api/token', [
      'headers' => [
        'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'grant_type' => 'client_credentials',
      ],
    ]);

    if ($request->getStatusCode() != 200) {
      return $request;
    }

    $result = json_decode($request->getBody()->getContents());
    $this->setAccessTokenValue($result->access_token);
    $this->setAccessTokenExpirationTimeValue($result->expires_in);
    $this->setAccessTokenCreatedAtValue();

    return $result;
  }

  /**
   * Get all tracks.
   *
   * @return array
   *  The response body with all songs or with errors.
   */
  function getSongs() {
    $request = $this->http_client->request('GET', 'https://api.spotify.com/v1/playlists/'. $this->getPlaylistId() .'/tracks', [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->getAccessToken(),
      ],
      'query' => [
        'limit' => 50,
      ],
    ]);

    if ($request->getStatusCode() != 200) {
      return $request;
    }

    $songs = json_decode($request->getBody()->getContents());
    return $songs;
  }

  /**
   * Get artist.
   *
   * @param string $artist_id
   * The artist id.
   *
   * @return array
   *  The response body with all songs or with errors.
   */
  function getArtist($artist_id) {
    $request = $this->http_client->request('GET', 'https://api.spotify.com/v1/artists/' . $artist_id, [
      'headers' => [
        'Authorization' => 'Bearer ' . $this->getAccessToken(),
      ],
    ]);

    if ($request->getStatusCode() != 200) {
      return $request;
    }

    $artist = json_decode($request->getBody()->getContents());
    return $artist;
  }

  /**
   * Set Access Token key value.
   *
   * @return bool
   *  The result of the key save.
   */
  function setAccessTokenValue($value) {
    // Set and save new message value.
    return $this->config_factory->set('spotify_access_token', $value)->save();
  }

  /**
   * Get the current access token.
   *
   */
  function getAccessToken() {
    return $this->config_factory->get('spotify_access_token');
  }

  /**
   * Set Access Token Expiration Time config value.
   *
   * @return bool
   *  The result of the config save.
   */
  function setAccessTokenExpirationTimeValue($value) {
    return $this->config_factory->set('spotify_access_token_expiration_time', $value)->save();
  }

  /**
   * Set Access Token Created At config value.
   *
   * @return bool
   *  The result of the config save.
   */
  function setAccessTokenCreatedAtValue() {
    return $this->config_factory->set('spotify_access_token_created_at', (new DateTime())->format('Y-m-d H:i:s'))->save();
  }

  /**
   * Get the current access token.
   *
   */
  function accessTokenIsExpired() {
    $created_at = strval($this->getAccessTokenCreatedAt());

    if (!$created_at) {
      return true;
    }

    $created_at = new DateTime(strval($this->getAccessTokenCreatedAt()));
    $now = (new DateTime());
    $access_token_age = $now->getTimestamp() - $created_at->getTimestamp();

    return $access_token_age > $this->getAccessTokenExpirationTime();
  }

  /**
   * Get the current access token expiration time.
   *
   */
  function getAccessTokenExpirationTime() {
    return $this->config_factory->get('spotify_access_token_expiration_time');
  }

  /**
   * Get the current access token expiration time.
   *
   */
  function getAccessTokenCreatedAt() {
    return $this->config_factory->get('spotify_access_token_created_at');
  }

  /**
   * Get the current client id.
   *
   */
  function getClientId() {
    return $this->config_factory->get('spotify_client_id');
  }

  /**
   * Get the current client secret.
   *
   */
  function getClientSecret() {
    return $this->config_factory->get('spotify_client_secret');
  }

  /**
   * Get the current access token.
   *
   */
  function getPlaylistId() {
    return $this->config_factory->get('spotify_playlist_id');
  }
}
