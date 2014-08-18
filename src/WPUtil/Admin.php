<?php

namespace WPUtil;

class Admin
{
  static public function hidePostType($post_type)
  {
    remove_menu_page("edit.php?post_type=$post_type");
  }

  static public function showPost($post_id, $title = null)
  {
    add_menu_page(
      $title ?: $post_id,
      $title ?: $post_id,
      'manage_options',
      "post.php?post=$post_id&action=edit"
    );
  }

  static public function hideTaxonomy($post_type, $taxonomy)
  {
    remove_submenu_page(
      "edit.php?post_type=$post_type",
      "edit-tags.php?taxonomy=$taxonomy&amp;post_type=$post_type"
    );
  }
}
