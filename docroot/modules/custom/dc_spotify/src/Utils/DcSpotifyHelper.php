<?php

namespace Drupal\dc_spotify\Utils;

use Drupal;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class DcSpotifyHelper {

    /**
   * Create or edit Node.
   *
   * @param array $fields
   *  The fields to be saved.
   *
   * @return Node
   *  The resulting Node.
   */
  public static function createNode($fields) {
    $node = Drupal::entityQuery('node')
    ->condition('field_spotify_id', $fields['field_spotify_id'])
    ->condition('type', $fields['type'])
    ->execute();

    if ($node) {
      $node = Node::load(reset($node));

      foreach ($fields as $key => $value) {
        $node->set($key, $value);
      }

      $node->save();

      return $node;
    }

    $node = Node::create($fields);
    $node->save();

    return $node;
  }

  /**
   * Create or get Term.
   *
   * @param string $term_name
   *  The Term name.
   *
   * @param string $vid
   *  The vocabulary ID.
   *
   * @return Term
   *  The resulting Term.
   */
  public static function createOrGetTerm($term_name, $vid) {
    $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => $term_name, 'vid' => $vid]);
    $term = reset($term);

    if ($term) {
      return $term;
    }

    $term = Term::create(['name' => $term_name, 'vid' => $vid,]);
    $term->save();

    return $term;
  }
}
