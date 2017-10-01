<?php

/**
 * @file
 * Contains Drupal\page_example\DemoEvent.
 */

namespace Drupal\page_example;

use Symfony\Component\EventDispatcher\Event;

class DemoEvent extends Event {

  protected $config;

  /**
   * Constructor.
   *
   * @param $config
   */
  public function __construct($config) {
    $this->config = $config;
  }

  /**
   * Getter for the config object.
   *
   * @return
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Setter for the config object.
   *
   * @param $config
   */
  public function setConfig($config) {
    $this->config = $config;
  }

}