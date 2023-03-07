<?php

namespace Drupal\custom_spotify_app\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the Song entity.
 *
 * @ingroup custom_spotify_app
 *
 * @ContentEntityType(
 *   id = "song",
 *   label = @Translation("Song"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\custom_spotify_app\Form\SongForm",
 *       "add" = "Drupal\custom_spotify_app\Form\SongForm",
 *       "edit" = "Drupal\custom_spotify_app\Form\SongForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "access" = "Drupal\custom_spotify_app\ArtistAccessControlHandler",
 *   },
 *   base_table = "song",
 *   admin_permission = "administer song",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/song/{song}",
 *     "edit-form" = "/admin/content/song/{song}/edit",
 *     "delete-form" = "/admin/content/song/{song}/delete",
 *     "collection" = "/admin/content/song"
 *   },
 *   field_ui_base_route = "entity.song.settings"
 * )
 */
class Song extends ContentEntityBase implements ContentEntityInterface {

    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add fields for the song entity.
      $fields['song'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the song.'))
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
    $fields['name'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Name'))
    ->setDescription(t('The name of the song.'))
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
    $fields['song_duration'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Title'))
    ->setDescription(t('The Name of the Song.'))
    ->setRequired(TRUE);
    $fields['song_title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The Name of the Song.'))
      ->setRequired(TRUE);

    $fields['song_album'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Cover Image'))
      ->setDescription(t('The album cover image.'))
      ->setRequired(TRUE);

    $fields['song_preview_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Spotify URL'))
      ->setDescription(t('The URL of the song on Spotify.'))
      ->setRequired(TRUE);

    $fields['player_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Player URL'))
      ->setDescription(t('The URL to embed the song player.'))
      ->setRequired(TRUE);

    return $fields;
  }
}
