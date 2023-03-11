<?php

namespace Drupal\custom_spotify_entities\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\custom_spotify_entities\ArtistInterface;
use Drupal\user\EntityOwnerTrait;
use Drupal\Core\Url;
use Drupal\link\LinkItemInterface;

/**
 * Defines the artist entity class.
 *
 * @ContentEntityType(
 *   id = "artist",
 *   label = @Translation("Artist"),
 *   label_collection = @Translation("Artists"),
 *   label_singular = @Translation("artist"),
 *   label_plural = @Translation("artists"),
 *   label_count = @PluralTranslation(
 *     singular = "@count artists",
 *     plural = "@count artists",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\custom_spotify_entities\ArtistListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\custom_spotify_entities\ArtistAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\custom_spotify_entities\Form\ArtistForm",
 *       "edit" = "Drupal\custom_spotify_entities\Form\ArtistForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "artist",
 *   data_table = "artist_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer artist",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/artist",
 *     "add-form" = "/artist/add",
 *     "canonical" = "/artist/{artist}",
 *     "edit-form" = "/artist/{artist}/edit",
 *     "delete-form" = "/artist/{artist}/delete",
 *   },
 *   field_ui_base_route = "entity.artist.settings",
 * )
 */
class Artist extends ContentEntityBase implements ArtistInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Name'))
      ->setRequired(TRUE)
      ->addConstraint('UniqueField')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['spotify_id'] = BaseFieldDefinition::create('string')
      ->setTranslatable(false)
      ->setLabel(t('ID Of the entity on Spotify'))
      ->setRequired(TRUE)
      ->addConstraint('UniqueField')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['spotify_detail_url'] = BaseFieldDefinition::create('link')
      ->setTranslatable(false)
      ->setLabel(t('Detail url on Spotify'))
      ->setSetting('title','link')
      ->setSetting('link_type', LinkItemInterface::LINK_EXTERNAL)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'link',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'link_default',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['followers'] = BaseFieldDefinition::create('integer')
      ->setTranslatable(TRUE)
      ->setLabel(t('Followers'))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'number',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['cover_image'] = BaseFieldDefinition::create('remote_image')
      ->setLabel(t('Artist Photo External'))
      ->setDescription(t('The Image for the Artist - external.'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'image',
        'weight' => -1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'remote_image',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['genre'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Genre'))
      ->setDescription(t('The Genre'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', [
        'target_bundles' => [
          'genre',
        ],
        'create_term' => TRUE,
      ])
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'taxonomy_term_reference',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the artist was created.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the artist was last edited.'));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'inline',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);


    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('Path'))
      ->setComputed(TRUE)
      ->setDisplayOptions('form', ['weight' => 100]);

    return $fields;
  }

}
