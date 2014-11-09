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

  /*
  // retrieve the unique post by name. create new post if a post not found

  $post_id = Post::uniquePost('post', 'hello', 'Hello World');
  */
  static public function uniquePost($post_type, $post_name, $post_title = null)
  {
    if (!(is_string($post_type) && strlen($post_type))) {
      throw new \Exception('$post_type required');
    }
    if (!(is_string($post_name) && strlen($post_name))) {
      throw new \Exception('$post_name required');
    }

    $query = new \WP_Query(array(
      'posts_per_page' => 1,
      'post_type' => $post_type,
      'name' => $post_name,
    ));

    if ($query->found_posts) {
      if ($query->posts) {
        return $query->posts[0]->ID;
      } else {
        return 0;
      }
    } else {
      return wp_insert_post(array(
        'post_type' => $post_type,
        'post_name' => $post_name,
        'post_title' => $post_title ?: '',
        'post_status' => 'publish',
      ));
    }
  }

  /*
  // get post by post id or post name
  */
  static public function getPost($post)
  {
    // falsy
    // -----

    if (!$post) {
      return null;
    }

    // WP_Post
    // -------

    if ($post instanceof \WP_Post) {
      return $post;
    }

    // id
    // --

    if (is_int($post)) {

      // cache

      $cache = wp_cache_get($post, __CLASS__ . '::' . __METHOD__);

      if ($cache !== false) {
        return $cache;
      }

      // get by id

      $post_ = get_post($post);

      wp_cache_set($post, $post_, __CLASS__ . '::' . __METHOD__);

      return $post_;
    }

    // slug
    // ----

    if (is_string($post)) {

      // cache

      $cache = wp_cache_get($post, __CLASS__ . '::' . __METHOD__);

      if ($cache !== false) {
        return $cache;
      }

      // postname

      $query = new \WP_Query([
        'pagename' => $post,
        'posts_per_page' => 1,
      ]);

      if (isset($query->posts[0])) {
        wp_cache_set($post, $query->posts[0], __CLASS__ . '::' . __METHOD__);
        return $query->posts[0];
      }

      // name

      $query = new \WP_Query([
        'name' => $post,
        'posts_per_page' => 1,
      ]);

      if (isset($query->posts[0])) {
        wp_cache_set($post, $query->posts[0], __CLASS__ . '::' . __METHOD__);
        return $query->posts[0];
      }

      // not found

      wp_cache_set($post, null, __CLASS__ . '::' . __METHOD__);

      return null;
    }

    return null;
  }
}
