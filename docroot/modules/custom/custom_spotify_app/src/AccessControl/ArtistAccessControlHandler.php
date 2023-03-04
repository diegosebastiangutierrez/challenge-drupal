<?php

namespace Drupal\custom_spotify_app\AccessControl;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Artist entity.
 *
 * @see \Drupal\custom_spotify_app\Entity\Artist.
 */
class ArtistAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view artist entities');

      case 'edit':
        return AccessResult::allowedIfHasPermission($account, 'edit artist entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete artist entities');

      case 'create':
        return AccessResult::allowedIfHasPermission($account, 'add artist entities');
    }

    // No opinion.
    return AccessResult::neutral();
  }

}
