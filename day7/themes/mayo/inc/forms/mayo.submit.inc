<?php
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
/**
 * @file
 * Process all form values. Uses multiple other include files and helper
 * functions to handle various operations.
 */
global $_path_to_mayo;
$_path_to_mayo = drupal_get_path('theme', 'mayo');

// Helper functions for processing the page layout.
include($_path_to_mayo . '/inc/forms/mayo.submit.builders.inc');

/**
 * Custom submit function - this mostly builds and saves stylesheets for
 * various features such as the responsive layout and font styles.
 *
 * @param $form
 * @param FormStateInterface $form_state
 */
function mayo_settings_submit($form, FormStateInterface $form_state) {
  global $_path_to_mayo;

  // Set form_state values into one variable
  $values = $form_state->getValues();

  // Get the default theme name, not the admin theme
  $theme_name = $form_state->getBuildInfo()['args'][0];

  // Set the path variable to the right path
  $path = 'public://mayo/' . $theme_name . '_files';
  $values['path'] = $path;

  // TODO HERE: Set up the files directory for the generated files
  if (file_prepare_directory($path, FILE_CREATE_DIRECTORY) == 1) {
  //variable_set('theme_' . $theme_name . '_files_directory', $path);
  \Drupal::configFactory()->getEditable('mayo.settings')->set('theme_' . $theme_name . '_files_directory', $path)
    ->save();
  }
  // Include processing for page & panels layouts, and responsive styles
  require_once($_path_to_mayo . '/inc/forms/mayo.submit.responsive.inc');
  mayo_submit_responsive($values, $theme_name, $path);
}
