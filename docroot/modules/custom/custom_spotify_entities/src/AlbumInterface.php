<?php

namespace Drupal\custom_spotify_entities;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an album entity type.
 */
interface AlbumInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
