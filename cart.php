<?php

$kirby->set('blueprint', 'cart', __DIR__ . DS . 'blueprints' . DS . 'cart.yml');
$kirby->set('blueprint', 'carts', __DIR__ . DS . 'blueprints' . DS . 'carts.yml');
$kirby->set('blueprint', 'item', __DIR__ . DS . 'blueprints' . DS . 'item.yml');

$kirby->set('route', array(
  'pattern' => c::get('cart.endpoint', 'cart') . '/update/(:all)',
  'action' => function($id) use($kirby) {
    try {

      $item = cart()->find($id);
      $data = array_intersect_key(get('data'), array_flip(array('amount')));
      $item->update($data);

    } catch(Exception $e) {

      if(r::ajax()) {
        return response::json(array(
          'success' => false,
          'message' => $e->getMessage(),
        ));
      }

    }

    if(r::ajax()) {
      return response::json(array(

      ));
    }

    return go(get('_redirect'));
  },
));

$kirby->set('route', array(
  'pattern' => c::get('cart.endpoint', 'cart') . '/destroy',
  'action' => 'LukasKleinschmidt\Cart\Controller::destroy',
));

$kirby->set('route', array(
  'pattern' => c::get('cart.parent.uid', 'carts') . '/(:all?)',
  'action' => function() {
    return site()->visit('error');
  },
));

// c::get('cart.endpoint', 'api/cart');
// c::get('cart.persitent', true);
// c::get('cart.parent.uid', 'carts');

// cart::add($item); // add a new item by uri
// cart::remove($item); // remove an item uri
// cart::items(); // list all items
// cart::destroy(); // destroy cart
