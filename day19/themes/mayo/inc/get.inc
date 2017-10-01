<?php

/**
 * @file
 * Provides frequently used functions that get theme info, settings and
 * other data.
 */

/**
 * Return the info file array for a particular theme, usually the active theme.
 * Simple wrapper function for list_themes().
 *
 * @param $theme_name
 */
function mayo_get_info($theme_name) {
  $info = &drupal_static(__FUNCTION__, array());
  if (empty($info)) {
    $lt = list_themes();
    foreach ($lt as $key => $value) {
      if ($theme_name == $key) {
        $info = $lt[$theme_name]->info;
      }
    }
  }

  return $info;
}

/**
 * Returns an array keyed by theme name.
 *
 * Return all the info file data for a particular theme including base
 * themes.
 *
 * @param $theme_name, usually the active theme.
 */
function mayo_get_info_trail($theme_name) {
  $info_trail = &drupal_static(__FUNCTION__, array());
  if (empty($info_trail)) {
    $theme_handler = \Drupal::service('theme_handler');
    $lt = $theme_handler->listInfo();  // Get a list of available themes.
    // First check for base themes and get info
    $base_theme = array();
    $ancestor = $theme_name;
    while ($ancestor && isset($lt[$ancestor]->base_theme)) {
      $ancestor = $lt[$ancestor]->base_theme;
      $base_theme[] = $lt[$ancestor];
    }
    foreach ($base_theme as $base) {
    $info_trail[$base->getName()]['info'] = $base->info;
    }

    // Now the active theme
    $info_trail[$theme_name]['info'] = $lt[$theme_name]->info;
  }
  return $info_trail;
}
