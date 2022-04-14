<?php

namespace Drupal\display_text_module\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\display_text_module\DisplayTextInterface;

/**
 * Defines the display_text entity.
 *
 * @ingroup display_text
 *
 * @ContentEntityType(
 *   id = "display_text",
 *   label = @Translation("[Xtrax] Slider display text"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\display_text_module\DisplayTextListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "storage_schema" = "Drupal\display_text_module\DisplayTextStorageSchema",
 *     "form" = {
 *       "default" = "Drupal\display_text_module\Form\DisplayTextForm",
 *       "add" = "Drupal\display_text_module\Form\DisplayTextForm",
 *       "edit" = "Drupal\display_text_module\Form\DisplayTextForm",
 *       "delete" = "Drupal\display_text_module\Form\DisplayTextDeleteForm",
 *     },
 *     "access" = "Drupal\display_text_module\DisplayTextAccessControlHandler",
 *   },
 *   base_table = "display_text",
 *   admin_permission = "administer display_text entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *   },
 *   links = {
 *     "canonical" = "/display_text/{display_text}",
 *     "edit-form" = "/display_text/{display_text}/edit",
 *     "delete-form" = "/display_text/{display_text}/edit",
 *     "collection" = "/admin/structure/display_texts"
 *   },
 *   field_ui_base_route = "entity.display_text.collection",
 * )
 */
class DisplayText extends ContentEntityBase implements DisplayTextInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t(''))
      ->setDefaultValue('')
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['size'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Size'))
      ->setDescription(t(''))
      ->setRequired(TRUE)
      ->setDefaultValue('normal')
      ->setSetting('allowed_values', [
        'normal' => 'Normal',
        'small' => 'Small',
        'big' => 'Big',

      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'list_default',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['weight'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Weight'))
      ->setDescription(t(''))
      ->setRequired(TRUE)
      ->setDefaultValue('normal')
      ->setSetting('allowed_values', [
        'normal' => 'Normal',
        'bold' => 'Bold',

      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'list_default',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
