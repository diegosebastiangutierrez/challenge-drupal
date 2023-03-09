<?php

namespace Drupal\custom_spotify_entities\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the song entity edit forms.
 */
class SongForm extends ContentEntityForm {

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
        $this->messenger()->addStatus($this->t('New song %label has been created.', $message_arguments));
        $this->logger('custom_spotify_entities')->notice('Created new song %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The song %label has been updated.', $message_arguments));
        $this->logger('custom_spotify_entities')->notice('Updated song %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.song.canonical', ['song' => $entity->id()]);

    return $result;
  }

}
