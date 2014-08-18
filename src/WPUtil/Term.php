<?php

namespace WPUtil;

class Term
{
  /* create terms

  $items = array(
    array('name' => 'Textile', 'slug' => 'textile'),
    array('name' => 'Basket',  'slug' => 'basket', 'terms' => array(
      array('name' => 'Nigeria', 'slug' => 'basket-nigeria'),
      array('name' => 'Ghana',   'slug' => 'basket-ghana'),
    )),
    array('name' => 'Yukata',  'slug' => 'yukata'),
    array('name' => 'Mask',    'slug' => 'mask'),
  );

  Term::terms($items, array('taxonomy' => 'custom-category'));
  */
  static public function terms($terms, $options = null)
  {
    $options = array_merge(array(
      'taxonomy' => 'category',
      'parent' => 0,
    ), $options ?: array());

    foreach ($terms as $term) {
      $term = array_merge(array(
        'taxonomy' => $options['taxonomy'],
      ), $term);

      $exists = term_exists($term['slug'], $term['taxonomy']);

      if (!$exists) {
        $exists = wp_insert_term($term['name'], $term['taxonomy'], array(
          'slug' => $term['slug'],
          'parent' => intval($options['parent']),
        ));
      }

      if (isset($term['terms'])) {
        Term::terms($term['terms'], array(
          'taxonomy' => $options['taxonomy'],
          'parent' => $exists['term_id'],
        ));
      }
    }
  }
}
