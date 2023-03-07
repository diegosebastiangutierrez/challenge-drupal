<?php

namespace Drupal\custom_spotify_app\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Defines the Artist entity.
 *
 * @ingroup custom_spotify_app
 *
 * @ContentEntityType(
 *   id = "artist",
 *   label = @Translation("Artist"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "form" = {
 *       "default" = "Drupal\custom_spotify_app\Form\ArtistForm",
 *       "add" = "Drupal\custom_spotify_app\Form\ArtistForm",
 *       "edit" = "Drupal\custom_spotify_app\Form\ArtistForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "access" = "Drupal\custom_spotify_app\ArtistAccessControlHandler",
 *   },
 *   base_table = "artist",
 *   admin_permission = "administer artist",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/artist/{artist}",
 *     "edit-form" = "/admin/content/artist/{artist}/edit",
 *     "delete-form" = "/admin/content/artist/{artist}/delete",
 *     "collection" = "/admin/content/artist"
 *   },
 *   field_ui_base_route = "entity.artist.settings"
 * )
 */
class Artist extends ContentEntityBase implements ContentEntityInterface {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add fields for the Artist entity.
      $fields['artist_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Artist.'))
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

    $fields['artist_name'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Name'))
    ->setDescription(t('The name of the Artist.'))
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

    $fields['artist_genres'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Genres'))
    ->setDescription(t(' genres.'))
    ->setRequired(TRUE);

    $fields['artist_popularity'] = BaseFieldDefinition::create('string')
    ->setLabel(t('popularity'))
    ->setDescription(t(' popularity.'))
    ->setRequired(TRUE);

    $fields['artist_image'] = BaseFieldDefinition::create('image')
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
}
