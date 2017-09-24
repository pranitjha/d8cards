<?php

/**
 * @file
 * Page Layout CSS Builder.
 */

global $theme_name;
$theme_name = \Drupal::theme()->getActiveTheme()->getName();
$_path_to_mayo = drupal_get_path('theme', 'mayo');
require_once($_path_to_mayo . '/inc/plugins.inc');

/**
 * Build Page Layouts
 *
 * Unlike the Panels layouts which hold CSS in their data array the Page layout
 * plugins each include a unique CSS builder function. This is required because
 * page layouts are all bespoke and can accept arbitrary user input for the
 * sidebar widths and must support three value units - pixles, em's and
 * percentages.  In other words building a one-size-fits-all builder function
 * would be overly complex and its far more flexible for themers to be able to
 * define thier own.
 *
 * As values come in from the submit function they are dispatched to the right
 * builder function.
 *
 * @param $method, tells the function which layout builder function to call.
 * @param $sidebar_first, an arbitrary numeric value.
 * @param $sidebar_second, an arbitrary numeric value.
 * @param $sidebar_unit, one of px, em or %.
 * @param $theme_name, the active theme.
 *
 * @see three_col_grail_layout() for an example of a builder function with docs.
 */
function mayo_build_page_layout($method, $sidebar_first, $sidebar_second, $sidebar_unit, $theme_name = NULL) {
  // Use the passed in theme_name, else grab it from global $theme_key
  if ($theme_name == NULL) {

    $theme_name = \Drupal::theme()->getActiveTheme()->getName();
  }

  $output = '';

  $builder_functions = page_layouts_data_structure($theme_name);
  foreach ($builder_functions as $function_prefix => $redundant_values) {
    if ($method === $function_prefix) {
      $function = $function_prefix . '_layout';
      $output = $function($sidebar_first, $sidebar_second, $sidebar_unit);
    }
  }
  return $output;
}
