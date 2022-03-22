<?php

namespace Drupal\display_text_module;

use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the display_text schema handler.
 */
class DisplayTextStorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getSharedTableFieldSchema(FieldStorageDefinitionInterface $storage_definition, $table_name, array $column_mapping) {
    $schema = parent::getSharedTableFieldSchema($storage_definition, $table_name, $column_mapping);
    $field_name = $storage_definition->getName();
    $index_fields = [];
    if(in_array($field_name, $index_fields)){
      $this->addSharedTableFieldIndex($storage_definition, $schema, TRUE);
	}
    return $schema;
  }

}
