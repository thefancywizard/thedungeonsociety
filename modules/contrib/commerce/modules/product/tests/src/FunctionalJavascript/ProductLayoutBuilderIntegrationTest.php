<?php

namespace Drupal\Tests\commerce_product\FunctionalJavascript;

use Drupal\commerce_product\Entity\ProductVariationType;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * @group commerce
 */
class ProductLayoutBuilderIntegrationTest extends ProductWebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'field_ui',
    'layout_discovery',
    'layout_builder',
    'commerce_cart',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function getAdministratorPermissions() {
    return array_merge([
      'access contextual links',
      'configure any layout',
      'administer commerce_product display',
    ], parent::getAdministratorPermissions());
  }

  /**
   * Tests that enabling Layout Builder for a display disables field injection.
   */
  public function testFieldInjectionDisabled() {
    $variation_view_display = commerce_get_entity_display('commerce_product_variation', 'default', 'view');
    $variation_view_display->setComponent('sku', [
      'label' => 'hidden',
      'type' => 'string',
    ]);
    $variation_view_display->save();

    $product = $this->createEntity('commerce_product', [
      'type' => 'default',
      'title' => $this->randomMachineName(),
      'stores' => $this->stores,
      'body' => ['value' => 'Testing product variation field injection!'],
      'variations' => [
        $this->createEntity('commerce_product_variation', [
          'type' => 'default',
          'sku' => 'INJECTION-DEFAULT',
          'price' => [
            'number' => '9.99',
            'currency_code' => 'USD',
          ],
        ]),
      ],
    ]);
    $this->drupalGet($product->toUrl());
    $this->assertSession()->pageTextContains('$9.99');
    $this->assertSession()->pageTextContains('INJECTION-DEFAULT');

    $this->enableLayoutsForBundle('default');

    $this->drupalGet($product->toUrl());
    $this->assertSession()->pageTextNotContains('$9.99');
    $this->assertSession()->pageTextNotContains('INJECTION-DEFAULT');
  }

  /**
   * Tests configuring the default layout for a product type.
   */
  public function testConfiguringDefaultLayout() {
    $this->enableLayoutsForBundle('default');
    $this->configureDefaultLayout('default');
  }

  /**
   * Tests that configuring the default layout doesn't generate multiple images.
   *
   * @link https://www.drupal.org/project/commerce/issues/3190799
   */
  public function testSampleValuesGeneratedImages() {
    // Add an image field to the variation.
    FieldStorageConfig::create([
      'entity_type' => 'commerce_product_variation',
      'field_name' => 'field_images',
      'type' => 'image',
      'cardinality' => 1,
    ])->save();
    $field_config = FieldConfig::create([
      'entity_type' => 'commerce_product_variation',
      'field_name' => 'field_images',
      'bundle' => 'default',
    ]);
    $field_config->save();

    $file_storage = \Drupal::entityTypeManager()->getStorage('file');
    // Assert the baseline file count.
    $this->assertEquals(0, $file_storage->getQuery()->accessCheck(FALSE)->count()->execute());

    $this->enableLayoutsForBundle('default');
    $this->configureDefaultLayout('default');

    // We should have one randomly generated image, for the variation.
    // @todo we end up with 5. I think it's due to the sample generated Product
    //   having sample variations also referenced.
    $files = $file_storage->loadMultiple();
    $this->assertCount(5, $files);
  }

  /**
   * Make sure products without a variation do not crash.
   */
  public function testProductWithoutVariationsDoesNotCrash() {
    $this->enableLayoutsForBundle('default', TRUE);
    $this->configureDefaultLayout('default');

    $product = $this->createEntity('commerce_product', [
      'type' => 'default',
      'title' => $this->randomMachineName(),
      'stores' => $this->stores,
      'body' => ['value' => 'Testing product does not crash!'],
    ]);
    $this->drupalGet($product->toUrl());
    $this->assertSession()->pageTextContains('Testing product does not crash!');
  }

  /**
   * Tests configuring a layout override for a product.
   */
  public function testConfiguringOverrideLayout() {
    $this->enableLayoutsForBundle('default', TRUE);
    $this->configureDefaultLayout('default');

    $product = $this->createEntity('commerce_product', [
      'type' => 'default',
      'title' => $this->randomMachineName(),
      'stores' => $this->stores,
      'body' => ['value' => 'Testing product variation field injection!'],
      'variations' => [
        $this->createEntity('commerce_product_variation', [
          'type' => 'default',
          'sku' => 'INJECTION-DEFAULT',
          'price' => [
            'number' => '9.99',
            'currency_code' => 'USD',
          ],
        ]),
      ],
    ]);
    $this->drupalGet($product->toUrl());
    $this->assertSession()->pageTextNotContains('INJECTION-DEFAULT');
    $this->clickLink('Layout');
    $this->assertSession()->pageTextContains('You are editing the layout for this Default product.');
    $this->addBlockToLayout('SKU');
    $this->getSession()->getPage()->pressButton('Save layout');
    $this->assertSession()->pageTextContains('The layout override has been saved.');

    $this->drupalGet($product->toUrl());
    $this->assertSession()->pageTextContains('INJECTION-DEFAULT');
  }

  /**
   * Test field injection on a Layout Builder enabled product.
   *
   * @group debug
   */
  public function testFieldInjectionOverAjax() {
    $variation_type = ProductVariationType::load('default');
    $variation_type->setGenerateTitle(FALSE);
    $variation_type->save();

    // Use the title widget so that we do not need to use attributes.
    $order_item_form_display = EntityFormDisplay::load('commerce_order_item.default.add_to_cart');
    $order_item_form_display->setComponent('purchased_entity', [
      'type' => 'commerce_product_variation_title',
    ]);
    $order_item_form_display->save();

    $first_variation = $this->createEntity('commerce_product_variation', [
      'title' => 'First variation',
      'type' => 'default',
      'sku' => 'first-variation',
      'price' => [
        'number' => 10,
        'currency_code' => 'USD',
      ],
    ]);
    $second_variation = $this->createEntity('commerce_product_variation', [
      'title' => 'Second variation',
      'type' => 'default',
      'sku' => 'second-variation',
      'price' => [
        'number' => 20,
        'currency_code' => 'USD',
      ],
    ]);
    $product = $this->createEntity('commerce_product', [
      'type' => 'default',
      'title' => $this->randomMachineName(),
      'stores' => $this->stores,
      'body' => ['value' => 'Testing product variation field injection!'],
      'variations' => [
        $first_variation,
        $second_variation,
      ],
    ]);

    $this->enableLayoutsForBundle('default');
    $this->configureDefaultLayout('default');

    $this->drupalGet($product->toUrl());

    $price_field_selector = '.block-field-blockcommerce-product-variationdefaultprice';
    $block_elements = $this->cssSelect($price_field_selector);
    // Should be exactly one of these in there.
    $this->assertCount(1, $block_elements);
    $this->assertSession()->elementTextContains('css', $price_field_selector . ' .field__item', '$10');
    $this->assertSession()->fieldValueEquals('purchased_entity[0][variation]', $first_variation->id());
    $this->getSession()->getPage()->selectFieldOption('purchased_entity[0][variation]', $second_variation->id());
    $this->assertSession()->assertWaitOnAjaxRequest();
    $this->assertSession()->elementTextContains('css', '.field--type-commerce-price', '$20');

    $this->getSession()->getPage()->selectFieldOption('purchased_entity[0][variation]', $first_variation->id());
    $this->assertSession()->assertWaitOnAjaxRequest();
    $this->assertSession()->elementTextContains('css', '.field--type-commerce-price', '$10');
  }

  /**
   * Configures a default layout for a product type.
   *
   * @param string $bundle
   *   The bundle to configure.
   */
  protected function configureDefaultLayout($bundle) {
    $this->drupalGet(Url::fromRoute('entity.entity_view_display.commerce_product.default', [
      'commerce_product_type' => $bundle,
    ]));
    $this->getSession()->getPage()->clickLink('Manage layout');
    $this->assertSession()->pageTextNotContains('$9.99');

    $this->addBlockToLayout('Price', function () {
      $this->assertSession()->pageTextContainsOnce('Currency display');
      $this->getSession()->getPage()->checkField('Strip trailing zeroes after the decimal point.');
    });

    $this->assertSession()->pageTextContainsOnce('$9.99');

    $this->addBlockToLayout('Variations', function () {
      $this->getSession()->getPage()->selectFieldOption('Label', '- Hidden -');
      $this->getSession()->getPage()->selectFieldOption('Formatter', 'Add to cart form');
    });

    $save_layout = $this->getSession()->getPage()->findButton('Save layout');
    $save_layout->focus();
    $save_layout->click();
    $this->assertSession()->pageTextContains('The layout has been saved.');
  }

  /**
   * Enable layouts.
   *
   * @param string $bundle
   *   The product bundle.
   * @param bool $allow_custom
   *   Whether to allow custom layouts.
   */
  protected function enableLayoutsForBundle($bundle, $allow_custom = FALSE) {
    $this->drupalGet(Url::fromRoute('entity.entity_view_display.commerce_product.default', [
      'commerce_product_type' => $bundle,
    ]));
    $this->getSession()->getPage()->checkField('layout[enabled]');
    if ($allow_custom) {
      $this->getSession()->getPage()->checkField('layout[allow_custom]');
    }
    $this->getSession()->getPage()->pressButton('Save');
    $this->assertNotEmpty($this->assertSession()->waitForElementVisible('css', '#edit-manage-layout'));
    $this->assertSession()->linkExists('Manage layout');
  }

  /**
   * Adds a block to the layout.
   *
   * @param string $block_title
   *   The block title which will be the link text.
   * @param callable|null $configure
   *   A callback that is invoked to configure the block.
   */
  protected function addBlockToLayout($block_title, callable $configure = NULL) {
    $assert_session = $this->assertSession();
    $assert_session->linkExists('Add block');
    $this->clickLink('Add block');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->linkExists($block_title);
    $this->clickLink($block_title);
    $assert_session->assertWaitOnAjaxRequest();
    if ($configure !== NULL) {
      $configure();
    }
    $assert_session->waitForElementVisible('named', ['button', 'Add block'])->press();
    $assert_session->assertWaitOnAjaxRequest();
  }

}
