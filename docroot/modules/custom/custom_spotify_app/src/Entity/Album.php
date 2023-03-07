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
    $fields['album_id'] = BaseFieldDefinition::create('string')
    ->setLabel(t('ID'))
    ->setDescription(t('The ID of the album.'))
    ->setRequired(TRUE)
    ->setSetting('max_length', 50)
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'string',
      'weight' => -5,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -5,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);

    $fields['album_artist'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Name of artist '))
    ->setDescription(t('The artist of the album.'))
    ->setRequired(TRUE)
    ->setSetting('max_length', 255)
    ->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'string',
      'weight' => -5,
    ])
    ->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -5,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);

    $fields['album_release_date'] = BaseFieldDefinition::create('datetime')
    ->setLabel(t('Album Release Date'))
    ->setDescription(t('The release date of the album.'))
    ->setRequired(TRUE)
    ->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'datetime_default',
      'weight' => -3,
    ])
    ->setDisplayOptions('form', [
      'type' => 'datetime_default',
      'weight' => -3,
    ])
    ->setDisplayConfigurable('form', TRUE)
    ->setDisplayConfigurable('view', TRUE);

    // Añadir ajustes adicionales para el campo de fecha
    $fields['album_release_date']->setSetting('datetime_type', 'date');
    $fields['album_release_date']->setSetting('date_format', 'Y-m-d');
    $fields['album_release_date']->setSetting('time_format', 'H:i:s');


    $fields['album_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the album.'))
      ->setRequired(TRUE);

    $fields['album_image'] = BaseFieldDefinition::create('image')
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
