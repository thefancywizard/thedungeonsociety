<?php

namespace Drupal\display_text_module;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of display_text entities.
 *
 * @see \Drupal\display_text_module\Entity\DisplayText
 */
class DisplayTextListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Label');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $label = !empty($entity->label()) ? $entity->label() : $entity->id();
	$row['label'] = new Link($label, Url::fromRoute("entity.display_text.canonical", ["display_text" => $entity->id()]));
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    return $operations;
  }

}

