<?php

namespace WPUtil;

class Option
{
  // set permalink structure
  static public function permalink($structure = '/%post_id%')
  {
    global $wp_rewrite;

    if ($wp_rewrite->permalink_structure !== $structure) {
      $wp_rewrite->set_permalink_structure($structure);
      $wp_rewrite->flush_rules();
    }
  }

  // disable comments and trackbacks
  static public function shutout()
  {
    update_option('default_comment_status', 'closed');
    update_option('default_ping_status', 'closed');
  }

  /* enable/disable plugins

  // enable akismet and hotfix
  Option::plugins(array(
    'akismet/akismet.php',
    'hotfix/hotfix.php',
  ));

  // you can omit *.php
  Option::plugins(array(
    'akismet',
    'hotfix',
  ));

  // disable all plugins
  Option::plugins();
  */
  static public function plugins($plugins)
  {
    $plugins = $plugins ? (is_array($plugins) ? $plugins : array($plugins)) : array();
    $plugins = array_map(function($plugin) {
      $i = strpos($plugin, '/');
      $j = strpos($plugin, '.');
      if ($i === false && $j === false) {
        return "$plugin/$plugin.php";
      } else {
        return $plugin;
      }
    }, $plugins);
    sort($plugins);
    update_option('active_plugins', $plugins);
  }

  // strip subdirectories from wordpress home
  static public function rootAsHome()
  {
    if (preg_match('/^(https?:\/\/[^\/]+)(\/.*)$/', get_option('home'), $matches)) {
      update_option('home', $matches[1]);
    }
  }
}
