<?php

namespace Drupal\extra_field\Plugin;

/**
 * Provides the Extra field plugin manager.
 */
interface ExtraFieldManagerBaseInterface {

  /**
   * Exposes the ExtraField plugins to hook_entity_extra_field_info().
   *
   * @return array
   *   The array structure is identical to that of the return value of
   *   \Drupal\Core\Entity\EntityFieldManagerInterface::getExtraFields().
   *
   * @see hook_entity_extra_field_info()
   */
  public function fieldInfo();

}
