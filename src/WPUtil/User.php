<?php

namespace WPUtil;

class User
{
  static public function self($meta)
  {
    $meta = array_merge(array(), $meta ?: array());
    $user = wp_get_current_user();
    foreach ($meta as $meta_key => $meta_value) {
      update_user_meta($user->ID, $meta_key, $meta_value);
    }
    return $user->ID;
  }
}
