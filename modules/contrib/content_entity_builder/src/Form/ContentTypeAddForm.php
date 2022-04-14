<?php

namespace Drupal\content_entity_builder\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Controller for content entity type addition forms.
 */
class ContentTypeAddForm extends ContentTypeFormBase {

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $id = trim($form_state->getValue('id'));
    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    $schema = \Drupal::database()->schema();
    if (array_key_exists($id, $entity_types) || $schema->tableExists($id)) {
      $form_state->setErrorByName('id', $this->t("Invalid machine-readable name. Enter a name other than %invalid.", ['%invalid' => $id]));
    }

  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Set default entity keys.
    $keys = [
      'id' => 'id',
      'uuid' => 'uuid',
    ];
    $this->entity->setEntityKeys($keys);
    parent::save($form, $form_state);
    \Drupal::messenger()->addMessage($this->t('Type %name was created.', ['%name' => $this->entity->label()]));

  }

  /**
   * {@inheritdoc}
   */
  public function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Create new types');

    return $actions;
  }

}
