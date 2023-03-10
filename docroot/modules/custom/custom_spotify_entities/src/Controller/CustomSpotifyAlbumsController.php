<?php

namespace Drupal\custom_spotify_entities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomSpotifyAlbumsController extends ControllerBase {

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
   * Returns the Spotify Artist page.
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

    $album_storage = $this->entityTypeManager->getStorage('album');
    $album_query = $album_storage->getQuery();
    $album_query->sort('title');
    $album_ids = $album_query->execute();

    foreach ($album_ids as $album_id) {
      $album = $album_storage->load($album_id);
      $artist = $album->getArtist();
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

}
