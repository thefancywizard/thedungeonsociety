<?php

namespace Drupal\display_text_module\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for display_text edit forms.
 *
 * @ingroup display_text_module
 */
class DisplayTextForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $element = parent::actions($form, $form_state);
    $entity = $this->entity;

    $account = \Drupal::currentUser();
    $type_id = $entity->getEntityTypeId();
    $element['delete']['#access'] = $account->hasPermission('delete display_text entity');
    $element['delete']['#weight'] = 100;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    // Build the entity object from the submitted values.
    $entity = parent::submit($form, $form_state);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        \Drupal::messenger()->addMessage($this->t('Created the %label.', [
            '%label' => $entity->label(),
          ]));

        break;

      default:
        \Drupal::messenger()->addMessage($this->t('Saved the %label.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect("entity.display_text.canonical", ["display_text" => $entity->id()]);
  }

}
