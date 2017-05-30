<?php

namespace LukasKleinschmidt\Cart;

use S;
use Exception;

class Cart {

  static public $instance;

  public $id;
  public $page;
  public $parent;

  public function __construct() {

    // configure session
    s::$cookie['lifetime'] = Settings::lifetime();
    s::$timeout            = Settings::timeout();

    // start session
    s::start();

    $this->id = s::id();

  }

  public static function instance() {
    if(!is_null(static::$instance)) return static::$instance;
    return static::$instance = new static();
  }

  /**
   * Get current session id
   *
   * @return string
   */
  public function id() {
    return $this->id;
  }

  /**
   * Get parent page which contains all carts
   *
   * @return Page
   */
  public function parent() {
    if(!is_null($this->parent)) return $this->parent;

    $parent = page(Settings::root());

    if(!$parent) {
      throw new Exception('Cart root does not exists');
    }

    return $this->parent = $parent;
  }

  /**
   * Get the related cart page or create a new page
   *
   * @return Page
   */
  public function page() {
    if(!is_null($this->page)) return $this->page;

    $page = $this->parent()->find($this->id());

    if(!$page) {
      $page = $this->create();
    }

    return $this->page = $page;
  }

  /**
   * Get cart subpage by uid
   *
   * @param string $uid
   * @return mixed Page or false
   */
  public function item($uid) {
    return $this->page()->find($uid);
  }

  /**
   * Get cart children
   *
   * @return Children
   */
  public function items() {
    return $this->page()->children();
  }

  /**
   * Checks if the cart page exists
   *
   * @return boolean
   */
  public function exists() {
    return $this->parent()->find($this->id()) ? true : false;
  }

  /**
   * Checks if the cart is empty
   *
   * @return boolean
   */
  public function empty() {
    return !$this->exists() || !$this->page()->children()->count();
  }

  /**
   * Create a new cart page
   *
   * @return Page
   */
  public function create($data = array()) {
    $parent = $this->parent();
    return $parent->create($parent->id() . '/' . $this->id(), Settings::blueprint(), $data);
  }

}
