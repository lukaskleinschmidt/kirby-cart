<?php

function cart() {

  s::start();

  $uid = s::id();

  return page('orders')->find($uid);

}
