<?php
/**
 * @file
 * Contains \Drupal\nvs_func\Twig\TimeAgo
 */
namespace Drupal\nvs_func\Twig;

class TimeAgo extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'getTimeAgo';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return array(
      new \Twig_SimpleFunction('getTimeAgo', array($this, 'getTimeAgo'), array(
        'is_safe' => array('html'),
        
      )),
    );
  }

  /**
   * Provides Kint function to Twig templates.
   *
   * Handles 0, 1, or multiple arguments.
   *
   * Code derived from https://github.com/barelon/CgKintBundle.
   *
   * @param Twig_Environment $env
   *   The twig environment instance.
   * @param array $context
   *   An array of parameters passed to the template.
   */
  public function getTimeAgo($timestamp) {


  return t('@time ago', array('@time' => \Drupal::service('date.formatter')->formatTimeDiffSince($timestamp)));
  }
}