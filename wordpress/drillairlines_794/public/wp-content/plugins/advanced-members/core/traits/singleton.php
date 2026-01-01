<?php
/**
 * Singleton Trait
 *
 * @since 1.0
 * 
 */
namespace AMem;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

trait Singleton {
  /**
   * Singleton Instance
   *
   * @var Singleton
   */
  protected static $instance = [];

  /**
   * Private Constructor
   *
   * We can't use the constructor to create an instance of the class
   *
   * @return void
   */
  private function __construct() {
    // Don't do anything, we don't want to be initialized
  }

  /**
   * Get the singleton instance
   * Use static::$instance to allow extended classes
   *
   * @return Singleton
   */
  public static function getInstance($args='') {
    $key = static::class;
    if ( $args )
      $key .= ':' . md5( maybe_serialize( $args ) );
    if (!isset(self::$instance[$key])) {
      self::$instance[$key] = new static($args);
    }

    return static::$instance[$key];
  }

  /**
   * Private clone method to prevent cloning of the instance of the
   * Singleton instance.
   *
   * @return void
   */
  private function __clone() {
    // Don't do anything, we don't want to be cloned
  }

  /**
   * Private unserialize method to prevent unserializing of the Singleton
   * instance.
   *
   * @return void
   */
  public function __wakeup() {
    // Don't do anything, we don't want to be unserialized
  }
}
