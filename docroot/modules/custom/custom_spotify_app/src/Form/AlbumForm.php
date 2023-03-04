<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the Album edit forms.
 *
 * @ingroup custom_spotify_app
 */
class AlbumForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label album.', [
        '%label' => $entity->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label album was not saved.', [
        '%label' => $entity->label(),
      ]), 'error');
    }

    $form_state->setRedirect('entity.album.canonical', ['album' => $entity->id()]);
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\custom_spotify_app\Entity\Album $album */
    $album = $this->entity;

    $form['#attached']['library'][] = 'custom_spotify_app/album_form';

    $form['title']['#description'] = $this->t('Enter the name of the album.');

    $form['field_album_cover_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Album cover image'),
      '#description' => $this->t('Upload the album cover image'),
      '#upload_location' => 'public://album_covers/',
      '#default_value' => isset($album->field_album_cover_image[0]) ? $album->field_album_cover_image[0] : '',
      '#required' => TRUE,
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
        'file_validate_size' => [5242880],
      ],
    ];

    $form['field_album_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Album title'),
      '#description' => $this->t('Enter the title of the album.'),
      '#maxlength' => 255,
      '#default_value' => $album->getTitle(),
      '#required' => TRUE,
    ];

    $form['field_album_artist'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'artist',
      '#title' => $this->t('Artist'),
      '#description' => $this->t('Enter the name of the artist or band associated with the album.'),
      '#default_value' => $album->field_album_artist->entity,
      '#required' => TRUE,
    ];

    $form['field_album_release_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Release date'),
      '#description' => $this->t('Enter the date the album was released.'),
      '#default_value' => $album->field_album_release_date->value,
      '#required' => TRUE,
    ];

    $form['field_spotify_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Spotify URL'),
      '#description' => $this->t('Enter the URL of the album on Spotify.'),
      '#maxlength' => 2048,
      '#default_value' => $album->field_spotify_url->uri,
      '#required' => TRUE,
    ];

    $form['field_album_songs'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'song',
      '#title' => $this->t('Songs'),
      '#description' => $this->t('Add the songs included in the album.'),
      '#default_value' => $album->field_album_songs->referencedEntities(),
      '#multiple' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */


}
