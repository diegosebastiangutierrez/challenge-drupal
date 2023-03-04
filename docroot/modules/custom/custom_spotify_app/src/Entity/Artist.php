<?php

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
class Artist extends ContentEntityBase implements ArtistInterface {
  // Entity code goes here.
}

