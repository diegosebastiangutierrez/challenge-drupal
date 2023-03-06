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

    // Add fields for the Album entity.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The Name of the Song.'))
      ->setRequired(TRUE);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Cover Image'))
      ->setDescription(t('The album cover image.'))
      ->setRequired(TRUE);

    $fields['spotify_url'] = BaseFieldDefinition::create('string')
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
