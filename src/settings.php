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

}
