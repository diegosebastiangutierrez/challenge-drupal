<?php

namespace Drupal\custom_spotify_entities\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the artist entity edit forms.
 */
class ArtistForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New artist %label has been created.', $message_arguments));
        $this->logger('custom_spotify_entities')->notice('Created new artist %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The artist %label has been updated.', $message_arguments));
        $this->logger('custom_spotify_entities')->notice('Updated artist %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.artist.canonical', ['artist' => $entity->id()]);

    return $result;
  }

}
