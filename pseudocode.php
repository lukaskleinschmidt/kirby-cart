<?php

$cart = cart();
$order = order();

field::$methods['filter'] = function($field, $key = null) {

  if(is_null($key)) {
    $filter = $field->page->filter();

    if(is_null($filter)) return $field;

    foreach($filter as $key) {
      return $field->filter($key);
    }
  }

  if(!isset($field->page->filtered[$field->key][$key])) return $field->page->filtered[$field->key][$key];

  $class  = $page->filter($key) . 'filter';
  $filter = new $class($field);

  $field->value = $filter->apply();

  return $field->page->filtered[$field->key][$key] = $field;

};




foreach($order->items() as $item) {
  $item->total()->filter();
  $item->total();
}









// $cart = new LukasKleinschmidt\Cart();
cartpage::$filter['percentage'] = function($cart, $amount) {
  $total = $cart->filtered('total') * $amount;
  return compact('total');
};

$cart->filter('percentage', 0.7);


// field::$methods['filter'] = function($field, $filter = null) {
//   if(is_null($filter)) {
//     $filter = item::$filter();
//   }
//   $field->value = $start . $field->value . $end;
//   return $field;
// };

// foreach(array_keys(item::$filter) as $key) {
//   if(v::in($key, $item->page()->product()->filter())) {
//     $item->filter($key);
//   }
// }

item::$filter['buy-3-get-1-free'] = function(Item $item) {
  return array(
    'total' => $item->filtered('total') - floor($item->quantity() / 3)) * $item->page()->price(),
  );
}

class Item {

  public static $filter = array();

}

class ItemPage extends Page {

  public $total;
  public $filtered;
  public $quantity;

  protected $data = array();

  public function page() {
    return page($this->page);
  }

  public function total() {
    if(!is_null($this->total) return $this->total;
    return $this->total = $this->page()->price() * $this->quantity();
  }

  /**
   * Check if a filter is apllied
   * @return boolean [description]
   */
  public function hasFilter($key) {

  }


  // /**
  //  * Get filtered value
  //  * @param  [type] $key [description]
  //  * @return [type]      [description]
  //  */
  // public function filtered($key) {
  //   if(isset($this->data[$key]) return $this->data[$key];
  //   return $this->data[$key] = $this->{$key}();
  // }

  /**
   * Apply required filter
   * @return [type] [description]
   */
  public function filtered() {
    if(isset($this->filtered]) return $this->filtered;
    return $this->filtered = new Filter($this, $this->filter());
  }

}

class Filter extends Silo {

  public $filter = array();

  public function __construct($page, $filter = array()) {
    foreach($filter as $key) {
      $this->apply($key);
    }
  }

  public function apply($key) {
    $filter = $this->get($key);

    if(!is_null($filter)) {
      $this->filter[] = $key;
    }

    $data = $filter($this);
  }

  public function __call($method, $arguments) {
    return isset($this->$method) ? $this->$method : null;
  }

}

filter::set('percentage', function() {
  $total = $this->total() * 0.7;
  return $field;
});

filter::set('buy-three-for-two', function() {
  $total = $this->total() - floor($this->quantity() / 3)) * $this->price();
  return compact('total');
});


?>
<form action="<?= $cart->url('update') ?>" method="post">
  <?php foreach($cart->items() as $item): ?>

    <span><?= $item->title(); ?></span>

    <?php if($item->hasFilter('buy-3-get-1-free')): ?>
      <span><?= $item->total(); ?>€</span> statt <span><?= $item->filtered()->total(); ?>€</span>
    <?php else: ?>
      <span><?= $item->total(); ?>€</span>
    <?php endif; ?>

    <?php if($item->hasFilter('buy-3-get-1-free')): ?>
      <p>It is cheaper because you bought 3 of this item</p>
    <?php endif; ?>

    <input type="number" name="<?= $item->name('amount'); ?>" value="<?= $item->amount(); ?>">
    <input type="hidden" name="_redirect" value="<?= $page->url(); ?>">

  <?php endforeach; ?>
  <button type="submit">Update</button>
</form>

<?php

$cart = cart();

?>

<form action="<?= $cart->action('update'); ?>">
  <div class="item">
    <?php foreach($cart->items() as $item): ?>

      <div class="item__title">
        <?= $item->title(); ?>
      </div>

      <?php if($item->isFiltered('total')): ?>
        <div class="item__price">
          <?= $item->total(); ?>€
          statt
          <?= $item->filtered()->total(); ?>€
        </div>
      <?php else: ?>
        <div class="item__price">
          <?= $item->total(); ?>€
        </div>
      <?php endif; ?>

      <?php foreach($item->filter() as $filter): ?>
        <div class="item__filter">
          <?= $filter; ?>
        </div>
      <?php endforeach;  ?>

      <input type="number" name="<?= $item->name('quantity'); ?>" value="<?= $item->quantity(); ?>">

    <?php endforeach;  ?>
  </div>
  <div class="cart">
    <div class="cart__total">
      <?= $cart->total(); ?>€

      <?php foreach($cart->filter() as $filter): ?>
        <div class="cart__filter">
          <?= $filter; ?>
        </div>
      <?php endforeach;  ?>
    </div>
  </div>
</form>
