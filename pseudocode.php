<?php
// Cart snippet
$cart = cart();

$cart = new LukasKleinschmidt\Cart();
$cart->filter(function() {

});


cart::$filter['30-percent-off'] = function(Cart $cart) {
  return array(
    'price' => $cart->total() * 0.7;
  )
}

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
    'total' => $item->filtered('total') - floor($item->amount() / 3)) * $item->page()->price(),
  );
}


class ItemPage extends Page {

  public static $filter = array();

  public $total;

  protected $data = array();

  public function page() {
    return page($this->page);
  }

  public function total() {
    if(!is_null($this->total) return $this->total;
    return $this->total = $this->page()->price() * $this->amount();
  }

  /**
   * Check if a filter is apllied
   * @return boolean [description]
   */
  public function hasFilter() {

  }

  /**
   * Apply filter
   * @return [type] [description]
   */
  public function filter() {

  }

  /**
   * Get filtered value
   * @param  [type] $key [description]
   * @return [type]      [description]
   */
  public function filtered($key) {
    if(isset($this->data[$key]) return $this->data[$key];
    return $this->data[$key] = $this->{$key}();
  }

  // public function

}

?>
<form action="<?= $cart->url('update') ?>" method="post">
  <?php foreach($cart->items() as $item): ?>

    <span><?= $item->title(); ?></span>

    <?php if($item->hasFilter('buy-3-get-1-free')): ?>
      <span><?= $item->total(); ?>€</span> statt <span><?= $item->filtered('total'); ?>€</span>
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
