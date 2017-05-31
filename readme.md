# Kirby Cart
Create session related pages. This repository is not meant to be a full blown cart solution. It only comes with a simple helper class. Therefore you need to add routes and/or controller logic by yourself. Use the `cart()` helper function to get the cart instance.

### Options
Available options and their default values.
```php
c::set('cart.root', 'carts');        // page containing carts
c::set('cart.blueprint', 'cart');    // cart blueprint
c::set('cart.session.timeout', 120); // session timeout
c::set('cart.session.lifetime', 0);  // session lifetime
```

### Exemplary routes
```php
$kirby->set('route', array(
  'pattern' => 'cart/add/(:all)',
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::add'
));

$kirby->set('route', array(
  'pattern' => array(
    'cart/increment/(:all)/(:num)',
    'cart/increment/(:all)',
  ),
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::increment'
));

$kirby->set('route', array(
  'pattern' => array(
    'cart/decrement/(:all)/(:num)',
    'cart/decrement/(:all)',
  ),
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::decrement'
));

$kirby->set('route', array(
  'pattern' => 'cart/update/(:all)/(:num)',
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::update'
));

$kirby->set('route', array(
  'pattern' => 'cart/delete/(:all)',
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::delete'
));

$kirby->set('route', array(
  'pattern' => 'cart.json',
  'method' => 'POST',
  'action' => 'LukasKleinschmidt\CartController::json'
));

```

### Exemplary controller
```php
namespace LukasKleinschmidt;

use V;
use R;
use Response;

class CartController {

  public static function add($uri) {

    $cart = cart();
    $page = page($uri);

    if(!$page) {
      return static::error();
    }

    if($cart->find($page->hash())) {
      static::increment($page->hash());
    } else {
      $items = $cart->children();
      $items->create($page->hash(), 'item', array(
        'quantity' => 1,
        'page' => $page->uri(),
      ))->sort($items->count());
    }

    if(r::ajax()) {
      return static::json();
    }

    static::redirect();

  }

  public static function delete($uid) {

    $item = cart()->find($uid);

    if(!$item) {
      return static::error();
    }

    $item->delete(true);

    static::sort();

    if(r::ajax()) {
      return static::json();
    }

    static::redirect();

  }

  public static function update($uid, $quantity) {

    $item = cart()->find($uid);

    if(!$item || !v::integer($quantity)) {
      return static::error();
    }

    if($quantity == 0) {
      return static::delete($item->hash());
    } else {
      $item->update(compact($quantity));
    }

    if(r::ajax()) {
      return static::json();
    }

    static::redirect();

  }

  public static function increment($uid, $by = 1) {

    $item = cart()->find($uid);

    if(!$item || !v::integer($by)) {
      return static::error();
    }

    $item->increment('quantity', $by);

    if(r::ajax()) {
      return static::json();
    }

    static::redirect();

  }

  public static function decrement($uid, $by = 1) {

    $item = cart()->find($uid);

    if(!$item || !v::integer($by)) {
      return static::error();
    }

    if($item->quantity()->value() - $by <= 0) {
      return static::delete($uid);
    } else {
      $item->decrement('quantity', $by);
    }

    if(r::ajax()) {
      return static::json();
    }

    static::redirect();

  }

  public static function json() {
    $cart  = cart();
    $page  = $cart->page();
    $items = array();

    foreach($cart->children() as $item) {
      $items[$item->hash()] = array(
        'modified' => $item->modified(),
        'quantity' => (int) $item->quantity()->value(),
        'total' => $item->total(),
      );
    }

    $data = array(
      'modified' => $item->modified(),
      'quantity' => $page->quantity(),
      'total' => $page->total(),
      'items' => $items,
    );

    return response::json($data);
  }

  public static function sort() {

    $num = 0;

    foreach(cart()->children() as $item) {
      $num++;
      $item->sort($num);
    }

  }

  public static function redirect() {
    if($url = get('_redirect')) go($url);
  }

  public static function error() {
    return site()->visit('error');
  }

}

```
