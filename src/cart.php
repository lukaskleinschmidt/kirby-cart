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
    if(!is_null($this->id)) return $this->id;
    return $this->id = s::id();
  }

  /**
   * Returns the parent page element
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
   * Returns the page element
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
   * Find any child or a set of children of this page
   *
   * @return Page | Children
   */
  public function find() {
    return call_user_func_array(array($this->children(), 'find'), func_get_args());
  }

  /**
   * Returns all children for this page
   *
   * @return Children
   */
  public function children() {
    return $this->page()->children();
  }

  /**
   * Checks if the cart exists
   *
   * @return boolean
   */
  public function exists() {
    return $this->parent()->find($this->id()) ? true : false;
  }

  /**
   * Checks if the page has children
   *
   * @return boolean
   */
  public function hasChildren() {
    return $this->exists() && $this->page()->children()->count();
  }

  /**
   * Create a new cart
   *
   * @return Page
   */
  public function create($data = array()) {
    $parent = $this->parent();
    return $parent->create($parent->id() . '/' . $this->id(), Settings::blueprint(), $data);
  }

  /**
   * Regenerate session id
   *
   * @return string
   */
  public function regenerateId() {

    // regenerate session id
    s::regenerateId();

    // new session id
    $id = s::id();

    if($this->exists()) {
      $this->page()->move($id);
    }

    return $this->id = $id;

  }

}
