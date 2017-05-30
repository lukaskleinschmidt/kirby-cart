<?php

namespace LukasKleinschmidt\Cart;

use C;

class Settings {

	public static function root() {
		return c::get('cart.root', 'carts');
	}

  public static function blueprint() {
    return c::get('cart.blueprint', 'cart');
  }

  public static function timeout() {
    return c::get('cart.session.timeout', 120);
  }

  public static function lifetime() {
    return c::get('cart.session.lifetime', 0);
  }

}
