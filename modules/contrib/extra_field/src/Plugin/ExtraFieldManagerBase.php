<?php

namespace Drupal\extra_field\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Base class for Extra Field plugin managers.
 *
 * @package Drupal\extra_field\Plugin
 */
abstract class ExtraFieldManagerBase extends DefaultPluginManager implements ExtraFieldManagerBaseInterface {

  /**
   * Caches bundles per entity type.
   *
   * @var array
   */
  protected $entityBundles;

  /**
   * {@inheritdoc}
   */
  abstract public function fieldInfo();

  /**
   * Clear the service's local cache.
   *
   * TODO Add this to the interface in the 3.0.0 release.
   */
  public function clearCache() {

    $this->entityBundles = [];
  }

  /**
   * Checks if the plugin bundle definition matches the entity bundle key.
   *
   * @param string[] $pluginBundles
   *   Defines which entity-bundle pair the plugin can be used for.
   *   Format: [entity type].[bundle] or [entity type].* .
   * @param string $entityBundleKey
   *   The entity-bundle string of a content entity.
   *   Format: [entity type].[bundle] .
   *
   * @return bool
   *   True of the plugin bundle definition matches the entity bundle key.
   */
  protected function matchEntityBundleKey(array $pluginBundles, $entityBundleKey) {

    $match = FALSE;
    foreach ($pluginBundles as $pluginBundle) {
      if (strpos($pluginBundle, '.*')) {
        $match = explode('.', $pluginBundle)[0] == explode('.', $entityBundleKey)[0];
      }
      else {
        $match = $pluginBundle == $entityBundleKey;
      }

      if ($match) {
        break;
      }
    }

    return $match;
  }

  /**
   * Returns entity-bundle combinations this plugin supports.
   *
   * If a wildcard bundle is set, all bundles of the entity will be included.
   *
   * @param string[] $entityBundleKeys
   *   Array of entity-bundle strings that define the bundles for which the
   *   plugin can be used. Format: [entity].[bundle]
   *   '*' can be used as bundle wildcard.
   *
   * @return array
   *   Array of entity and bundle names. Keyed by the [entity].[bundle] key.
   */
  protected function supportedEntityBundles(array $entityBundleKeys) {

    $result = [];
    foreach ($entityBundleKeys as $entityBundleKey) {
      if (strpos($entityBundleKey, '.') === FALSE) {
        continue;
      }

      list($entityType, $bundle) = explode('.', $entityBundleKey);
      if ($bundle == '*') {
        foreach ($this->allEntityBundles($entityType) as $bundle) {
          $key = $this->entityBundleKey($entityType, $bundle);
          $result[$key] = [
            'entity' => $entityType,
            'bundle' => $bundle,
          ];
        }
      }
      else {
        $result[$entityBundleKey] = [
          'entity' => $entityType,
          'bundle' => $bundle,
        ];
      }
    }

    return $result;
  }

  /**
   * Returns the bundles that are defined for an entity type.
   *
   * @param string $entityType
   *   The entity type to get the bundles for.
   *
   * @return string[]
   *   Array of bundle names.
   */
  protected function allEntityBundles($entityType) {

    if (!isset($this->entityBundles[$entityType])) {
      $bundleType = $this->getEntityBundleType($entityType);

      if ($bundleType) {
        $bundles = $this->getEntityBundles($bundleType);
      }
      else {
        $bundles = [$entityType => $entityType];
      }

      $this->entityBundles[$entityType] = $bundles;
    }

    return $this->entityBundles[$entityType];
  }

  /**
   * Build the field name string.
   *
   * @param string $pluginId
   *   The machine name of the Extra Field plugin.
   *
   * @return string
   *   Field name.
   */
  protected function fieldName($pluginId) {

    return 'extra_field_' . $pluginId;
  }

  /**
   * Creates a key string with entity type and bundle.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   *
   * @return string
   *   Formatted string.
   */
  protected function entityBundleKey($entityType, $bundle) {

    return "$entityType.$bundle";
  }

  /**
   * Returns the name of the entity type which provides bundles.
   *
   * @param string $entityType
   *   The entity type to get the data of.
   *
   * @return string|null
   *   The entity type or null if the entity does not provide bundles.
   */
  protected function getEntityBundleType($entityType) {
    return $this->getEntityTypeManager()
      ->getDefinition($entityType)
      ->getBundleEntityType();
  }

  /**
   * Returns all bundles of an entity type.
   *
   * @param string $entityType
   *   The entity type to get the data of.
   *
   * @return array
   *   Array of bundle names.
   */
  protected function getEntityBundles($entityType) {
    return $this->getEntityTypeManager()
      ->getStorage($entityType)
      ->getQuery()
      ->execute();
  }

  /**
   * Returns the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager service.
   */
  abstract protected function getEntityTypeManager();

}
