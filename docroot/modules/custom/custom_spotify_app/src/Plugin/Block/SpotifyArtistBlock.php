<?php

namespace Drupal\custom_spotify_app\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SpotifyArtistBlock' block.
 *
 * @Block(
 *  id = "spotify_artist_block",
 *  admin_label = @Translation("Spotify Artist block"),
 * )
 */
class SpotifyArtistBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // TODO: Add logic to retrieve artist information from the Spotify API.

    return $build;
  }

}

