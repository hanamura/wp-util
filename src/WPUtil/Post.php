<?php

namespace WPUtil;

class Post
{
  /* create published post

  $post_id = Post::post(
    'post',
    'hello-world',
    array('post_title' => 'Hello World'),
    array('custom_value' => 'Custom Value'),
  );
  */
  static public function post($type, $slug, $options = null, $meta = null)
  {
    // check existence
    $query = new \WP_Query(array(
      'posts_per_page' => 1,
      'post_type' => $type,
      'name' => $slug,
    ));

    if ($query->posts) {
      $id = $query->posts[0]->ID;

      if ($options) {
        $options = array_merge(array(), $options ?: array(), array('ID' => $id));
        wp_update_post($options);
      }
    } else {
      $options = array_merge(array(
        'post_status' => 'publish',
      ), $options ?: array(), array(
        'post_type' => $type,
        'post_name' => $slug,
      ));
      // prevent error when there is no specified post type
      $id = @wp_insert_post($options);
    }

    if ($id && $meta) {
      foreach ($meta as $meta_key => $meta_value) {
        update_post_meta($id, $meta_key, $meta_value);
      }
    }

    return $id;
  }
}
