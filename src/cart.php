<?php

namespace LukasKleinschmidt\Cart;

use S;

class Cart {

  static public $instance;

  public function __construct() {
    s::start();
    $this->id = s::id();
  }

  public static function instance() {
    if(!is_null(static::$instance)) return static::$instance;
    return static::$instance = new static();
  }

  public function id() {
    return $this->id;
  }

  public function parent() {
    $page = page(Settings::root());

    if(!$page) {
      throw new Exception('Cart root does not exists');
    }

    return $page;
  }

  public function page() {
    $page = $this->parent()->find($this->id());

    if(!$page) {
      $page = $this->create();
    }

    return $page;
  }

  public function item($uid) {
    return $this->page()->find($uid);
  }

  public function items() {
    return $this->page()->children();
  }

  public function empty() {
    return $this->parent()->find($this->id()) ? false : true;
  }

  public function create() {
    $parent = $this->parent();
    return $parent->create($parent->id() . '/' . $this->id(), Settings::blueprint());
  }
}
