<?php

namespace Drupal\custom_spotify_app\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the Album entity.
 *
 * @ingroup custom_spotify_app
 *
 * @ContentEntityType(
 *   id = "album",
 *   label = @Translation("Album"),
 *   base_table = "album",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *   },
 * )
 */
class Album extends ContentEntityBase implements ContentEntityInterface{

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add fields for the Album entity.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the album.'))
      ->setRequired(TRUE);

    $fields['cover_image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Cover Image'))
      ->setDescription(t('The album cover image.'))
      ->setRequired(TRUE);

    $fields['spotify_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Spotify URL'))
      ->setDescription(t('The URL of the album on Spotify.'))
      ->setRequired(TRUE);

    $fields['player_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Player URL'))
      ->setDescription(t('The URL to embed the album player.'))
      ->setRequired(TRUE);

    return $fields;
  }

  public function getSongs() {
    $songs = [];

    foreach ($this->get('field_album_songs')->referencedEntities() as $song) {
      $songs[] = $song;
    }

    return $songs;
  }

}
