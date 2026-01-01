<?php
/**
 * Abstract Module
 * 
 * @since  1.0
 * 
 */
namespace AMem;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

abstract class Module {
  use Singleton;

  protected $inc = '';

  protected $name = '';

  protected $url = '';

  protected $version = AMEM_VERSION;

  public $priority = 10;

  protected $allowed_keys = ['inc', 'name', 'version', 'priority'];

  function __construct() {
    // initialize class
  }

  public function __get($key) {
    if ( isset($this->$key) /*&& in_array( $key, (array)$this->allowed_keys, true )*/ )
      return $this->$key;

    return null;
  }

}