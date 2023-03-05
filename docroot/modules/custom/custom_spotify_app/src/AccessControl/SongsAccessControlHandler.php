<?php

namespace Drupal\custom_spotify_app\AccessControl;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Album entity.
 *
 * @see \Drupal\custom_spotify_app\Entity\Album.
 */
class SongsAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view song entities');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit song entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete song entities');

      case 'create':
        return AccessResult::allowedIfHasPermission($account, 'add song entities');
    }

    // No opinion.
    return AccessResult::neutral();
  }

}
