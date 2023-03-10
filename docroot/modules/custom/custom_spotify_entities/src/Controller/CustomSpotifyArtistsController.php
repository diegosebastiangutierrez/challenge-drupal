<?php

namespace Drupal\custom_spotify_entities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomSpotifyArtistsController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /* Constructs a new SongsController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the Spotify Artist list page.
   */
  public function list() {
    $build = [
      '#theme' => 'table',
      '#header' => [
        $this->t('Song'),
        $this->t('Album'),
        $this->t('Artist'),
      ],
      '#rows' => [],
    ];

    $artist_storage = $this->entityTypeManager->getStorage('artist');
    $artist_query = $artist_storage->getQuery();
    $artist_query->sort('name');
    $artist_ids = $artist_query->execute();

    foreach ($artist_ids as $artist_id) {
      $artist = $artist_storage->load($artist_id);
      $album = $artist->getAlbum();
      $album_cover = $album->getCoverUrl();

      $tracks = $album->getTracks();
      foreach ($tracks as $track) {
        $build['#rows'][] = [
          'data' => [
            $track->getTitle(),
            [
              'data' => [
                '#theme' => 'image',
                '#uri' => $album_cover,
                '#alt' => $album->getTitle(),
                '#width' => 100,
                '#height' => 100,
              ],
            ],
            [
              'data' => [
                '#type' => 'link',
                '#title' => $artist->getTitle(),
                '#url' => $artist->toUrl(),
              ],
            ],
          ],
        ];
      }
    }

    return $build;
  }

  /**
   * Returns the Spotify Artist page.
   */
  public function detail($id) {
    $build = [
      '#theme' => 'table',
      '#header' => [
        $this->t('Artist'),
        $this->t('Album'),
        $this->t('Song'),
      ],
      '#rows' => [],
    ];

    return $build;
  }

}
