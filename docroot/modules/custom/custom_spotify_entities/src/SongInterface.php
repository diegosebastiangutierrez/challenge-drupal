<?php

namespace Drupal\custom_spotify_entities;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a song entity type.
 */
interface SongInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
