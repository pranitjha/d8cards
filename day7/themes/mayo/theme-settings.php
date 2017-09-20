<?php
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\ThemeSettingsForm;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * Custom theme settings
 */
function mayo_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  global $base_url;
  //Change collapsible fieldsets (now details) to default #open => FALSE.
  $form['theme_settings']['#open'] = FALSE;
  $form['logo']['#open'] = FALSE;
  $form['favicon']['#open'] = FALSE;

  // Add various settings into drupalSettings for the live preview.
  $uri = theme_get_setting('logo.path', 'mayo');
  if (!empty($uri)) {
    $file_url = file_create_url($uri);
    $file_url = str_ireplace($base_url, '', $file_url);
  }
  else {
    $file_url = '/' . drupal_get_path('theme', 'mayo') . '/logo.svg';
  }
  $js = array(
      'logo_path' => $file_url,
      'header_bg_file' => theme_get_setting('header_bg_file', 'mayo'),
      'header_bg_alignment' => theme_get_setting('header_bg_alignment', 'mayo'),
      'header_watermark' => theme_get_setting('header_watermark', 'mayo'),
      'header_border_width' => theme_get_setting('header_border_width', 'mayo'),
      'layout_style' => theme_get_setting('layout_style', 'mayo'),
      'base_vmargin' => theme_get_setting('base_vmargin', 'mayo'),
    );
  $form['#attached']['drupalSettings']['color'] = $js;

  // Get our plugin system functions.
  require_once(drupal_get_path('theme', 'mayo') . '/inc/plugins.inc');

  // We need some getters.
  require_once(drupal_get_path('theme', 'mayo') . '/inc/get.inc');
  $path_to_mayo = drupal_get_path('theme', 'mayo');

  // General "alters" use a form id. Settings should not be set here. The only
  // thing useful about this is if you need to alter the form for the running
  // theme and *not* the theme setting.
  // @see http://drupal.org/node/943212
  if (isset($form_id)) {
    return;
  }

  // Get an array of device groups with option values
  $device_group_options = page_layouts_device_group_options('mayo');

  // Unit options
  $unit_options = array('%' => '%', 'px' => 'px', 'em' => 'em');

  // Assign $options for each device group
  foreach ($device_group_options as $device_group => $options) {

    // About here we need to call a custom sort function, this is what we got for now
    sort($options, SORT_STRING);

    foreach ($options as $option) {
      if ($device_group === 'bigscreen') {
        $bigscreen_options[$option] = \Drupal\Component\Utility\Unicode::ucfirst(str_replace('_', ' ', $option)); // human readable option names for accessibility
      }
      if ($device_group === 'tablet_landscape') {
        $tablet_landscape_options[$option] = \Drupal\Component\Utility\Unicode::ucfirst(str_replace('_', ' ', $option));
      }
      if ($device_group === 'tablet_portrait') {
        $tablet_portrait_options[$option] = \Drupal\Component\Utility\Unicode::ucfirst(str_replace('_', ' ', $option));
      }
      if ($device_group === 'smalltouch_landscape') {
        $smalltouch_landscape_options[$option] = \Drupal\Component\Utility\Unicode::ucfirst(str_replace('_', ' ', $option));
      }
    }
  }

  /* --------------- Font settings -------------- */
  $form['font'] = array(
    '#type' => 'details',
    '#title' => t('Font settings'),
    '#open' => FALSE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
  );
  $form['font']['base_font_size'] = array(
    '#type' => 'select',
    '#title' => t('Base font size'),
    '#default_value' => theme_get_setting('base_font_size'),
    '#options' => array(
      '75%'    => '75% (=12px)',
      '81.25%' => '81.25% (=13px)',
      '87.5%'  => '87.5% (=14px)',
      '93.75%' => '93.75% (=15px)',
      '100%'   => '100% (=16px)',
      '112.5%' => '112.5% (=18px)'
    ),
    '#description' => t('To support text size enlargement/reduction, percent ratio based on the browser\'s regular font size (which is mostly 16px) is used.'),
  );
  $form['font']['base_font_family'] = array(
    '#type' => 'select',
    '#title' => t('Base font family'),
    '#default_value' => theme_get_setting('base_font_family'),
    '#options' => array(
      0 => t('Serif: Georgia, Palatino Linotype, Book Antiqua, URW Palladio L, Baskerville, serif'),
      1 => t('Sans-Serif: Verdana, Geneva, Arial, Bitstream Vera Sans, DejaVu Sans, sans-serif'),
      2 => t('Custom'),
    ),
    '#description' => t('Font used for most part of the contents.'),
  );
  $form['font']['base_custom_font_family'] = array(
    '#type' => 'textfield',
    '#title' => t('Custom base font family'),
    '#default_value' => theme_get_setting('base_custom_font_family'),
    '#size' => 80,
    '#description' => t('Enter the base font-family you want to use. No need to start with <b>font-family:</b> and end with <b>;</b>. Just enter comma separated font names.'),
    '#prefix' => '<div id="base-custom-font-family-wrapper">',
    '#suffix' => '</div>',
  );
  $form['font']['heading_font_family'] = array(
    '#type' => 'select',
    '#title' => t('Heading font family (except for the site name and slogan)'),
    '#default_value' => theme_get_setting('heading_font_family'),
    '#options' => array(
      0 => t('Serif: Georgia, Palatino Linotype, Book Antiqua, URW Palladio L, Baskerville, serif'),
      1 => t('Sans-Serif: Verdana, Geneva, Arial, Bitstream Vera Sans, DejaVu Sans, sans-serif'),
      2 => t('Custom'),
    ),
    '#description' => t('Font used for the headings (h1, h2, h3, h4, h5). Font used for the site name and slogan can not be changed here. If you want to change it, please manually edit style.css in the theme\'s css subdirectory.'),
  );
  $form['font']['heading_custom_font_family'] = array(
    '#type' => 'textfield',
    '#title' => t('Custom heading font family'),
    '#default_value' => theme_get_setting('heading_custom_font_family'),
    '#size' => 80,
    '#description' => t('Enter the font-family you want to use for the headings. No need to start with <b>font-family:</b> and end with <b>;</b>. Just enter comma separated font names.'),
    '#prefix' => '<div id="heading-custom-font-family-wrapper">',
    '#suffix' => '</div>',
  );

  /* --------------- Layout settings -------------- */
  $form['layout'] = array(
    '#type' => 'details',
    '#title' => t('Layout settings'),
    '#collapsed' => TRUE,
    '#collapsible' => TRUE,
  );
  $form['layout']['base_vmargin'] = array(
    '#type' => 'textfield',
    '#title' => t('Base vertical (top/bottom) margin'),
    '#default_value' => theme_get_setting('base_vmargin'),
    '#size' => 12,
    '#maxlength' => 8,
    '#description' => t('Specify the base vertical (top/bottom) margin which is vertical spaces between page edge and browser screen in px.'),
    '#prefix' => '<img src="' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/base-layout.png')->toString() . '" /><br />',
  );
  $form['layout']['page_margin'] = array(
    '#type' => 'textfield',
    '#title' => t('Page margin'),
    '#default_value' => theme_get_setting('page_margin'),
    '#size' => 12,
    '#maxlength' => 8,
    '#description' => t('Specify the page margin which is spaces between page edge and contents in px.'),
  );
  $form['layout']['layout_style'] = array(
    '#type' => 'radios',
    '#title' => t('Layout style'),
    '#default_value' => theme_get_setting('layout_style'),
    '#options' => array(
      1 => t('1. Apply page margin to all (header, footer and main contents).'),
      2 => t('2. Apply page margin to main contents only.'),
    ),
    '#description' => '<img src="' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/page-layout.png')->toString() . '" /><br />' . t('When the layout 2 is selected, or header background image is selected, header borders are not drawn to make it look better.'),
  );

  /* --------------- Responsive sidebar layout settings -------------- */
  /* -----------Big screen as in desktop pc monitor------------- */
  $form['layout']['bigscreen'] = array(
    '#type' => 'details',
    '#title' => t('Big Screen Sidebar layout'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#attributes' => array(
      'class' => array('mayo-layout-form'),
    ),
  );

    // Big screen Layout
  $form['layout']['bigscreen']['bigscreen-layout-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Choose sidebar layout'),
  );
    // Options

  $form['layout']['bigscreen']['bigscreen-layout-wrapper']['bigscreen_layout'] = array(
     '#type' => 'radios',
     '#title' => t('<strong>Choose sidebar positions</strong>'),
     '#default_value' => str_replace('-', '_', theme_get_setting('bigscreen_layout')),
     '#options' => $bigscreen_options,
  );

  // Sidebars
  $form['layout']['bigscreen']['bigscreen-sidebar-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set sidebar widths'),
    '#description' => t('<strong>Set the width of each sidebar</strong>'),
    '#collapsible' => FALSE,
  );

  // Units
  $form['layout']['bigscreen']['bigscreen-sidebar-wrapper']['bigscreen_sidebar_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('bigscreen_sidebar_unit'),
    '#options' => $unit_options,
  );

  // Sidebar first
  $form['layout']['bigscreen']['bigscreen-sidebar-wrapper']['bigscreen_sidebar_first'] = array(
    '#type' => 'textfield',
    '#title' => t('First sidebar'),
    '#default_value' => Html::escape(theme_get_setting('bigscreen_sidebar_first')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'required' => array(
        array('input[name="bigscreen_layout"]' => array('value' => 'three_col_grail')),
        array('input[name="bigscreen_layout"]' => array('value' => 'two_sidebars_left')),
        array('input[name="bigscreen_layout"]' => array('value' => 'two_sidebars_right')),
      ),
    ),
  );

  // Sidebar second
  $form['layout']['bigscreen']['bigscreen-sidebar-wrapper']['bigscreen_sidebar_second'] = array(
    '#type' => 'textfield',
    '#title' => t('Second sidebar'),
    '#default_value' => Html::escape(theme_get_setting('bigscreen_sidebar_second')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'required' => array(
        array('input[name="bigscreen_layout"]' => array('value' => 'three_col_grail')),
        array('input[name="bigscreen_layout"]' => array('value' => 'two_sidebars_left')),
        array('input[name="bigscreen_layout"]' => array('value' => 'two_sidebars_right')),
      ),
    ),
  );

  // Page width
  $form['layout']['bigscreen']['bigscreen-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set the page width'),
    '#description' => t('<strong>Set the page width</strong>'),
  );

  // Unit
  $form['layout']['bigscreen']['bigscreen-width-wrapper']['bigscreen_page_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('bigscreen_page_unit'),
    '#options' => $unit_options,
  );

  // Width
  $form['layout']['bigscreen']['bigscreen-width-wrapper']['bigscreen_page_width'] = array(
    '#type'  => 'textfield',
    '#title' => t('Page width'),
    '#default_value' => Html::escape(theme_get_setting('bigscreen_page_width')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#required' => TRUE,
  );

  // Media queries
  $form['layout']['bigscreen']['media-queries-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Standard Screen Media Queries'),
    '#weight' => 1,
    '#attributes' => array(
      'class' => array('at-media-queries'),
    ),
  );

  // Media query
  $form['layout']['bigscreen']['media-queries-wrapper']['bigscreen_media_query'] = array(
    '#type' => 'textfield',
    '#title' => t('Media query for this layout'),
    '#default_value' => Html::escape(theme_get_setting('bigscreen_media_query')),
    '#description' => t('Do not include @media, it\'s included automatically.'),
    '#size' => 100,
    '#required' => TRUE,
  );

  /* ****************************************************************************
   *
   * Tablet
   *
   * ************************************************************************** */

  $form['layout']['tablet'] = array(
    '#type' => 'details',
    '#title' => t('Tablet Sidebar Layout'),
    '#description' => t('<h3>Tablet Layout</h3><p>Tablet devices such as iPad, Android and Windows tablets have two orientations - landscape and portrait, which can also be thought of as wide and narrow tablets. You can configure a different layout for each orientation.</p>'),
    '#attributes' => array(
      'class' => array('mayo-layout-form'),
    ),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  /* ******************
   * Tablet landscape
   * **************** */

  $form['layout']['tablet']['landscape'] = array(
    '#type' => 'details',
    '#title' => t('Landscape'),
    '#description' => t('<h4>Landscape tablet <span class="field-description-info">(wide)</span></h4>'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  // Tablet landscape Layout options
  $form['layout']['tablet']['landscape']['tablet-landscape-layout-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Choose sidebar layout'),
  );

  // Options
  $form['layout']['tablet']['landscape']['tablet-landscape-layout-wrapper']['tablet_landscape_layout'] = array(
    '#type' => 'radios',
    '#title' => t('<strong>Choose sidebar positions</strong>'),
    '#default_value' => str_replace('-', '_', theme_get_setting('tablet_landscape_layout')),
    '#options' => $tablet_landscape_options,
  );

  // Sidebars
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set sidebar widths'),
    '#description' => t('<strong>Set the width of each sidebar</strong>'),
  );

  // Units
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper']['tablet_landscape_sidebar_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('tablet_landscape_sidebar_unit'),
    '#options' => $unit_options,
  );

  // Sidebar first
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper']['tablet_landscape_sidebar_first'] = array(
    '#type' => 'textfield',
    '#title' => t('First sidebar'),
    '#default_value' => Html::escape(theme_get_setting('tablet_landscape_sidebar_first')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'required' => array(
        array('input[name="tablet_landscape_layout"]' => array('value' => 'three_col_grail')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_left')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_left_stack')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_right')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_right_stack')),
      ),
    ),
  );

  // Sidebar second
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper']['tablet_landscape_sidebar_second'] = array(
    '#type' => 'textfield',
    '#title' => t('Second sidebar'),
    '#default_value' => Html::escape(theme_get_setting('tablet_landscape_sidebar_second')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'invisible' => array(
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_left_stack')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_right_stack')),
      ),
      'required' => array(
        array('input[name="tablet_landscape_layout"]' => array('value' => 'three_col_grail')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_left')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_right')),
      ),
    ),
  );

  // Conditional messages for sidebar layouts
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper']['tablet-landscape-sidebar-message-wrapper'] = array(
    '#type' => 'details',
    '#states' => array(
      'invisible' => array(
        array('input[name="tablet_landscape_layout"]' => array('value' => 'three_col_grail')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_left')),
        array('input[name="tablet_landscape_layout"]' => array('value' => 'two_sidebars_right')),
      ),
    ),
  );
  $form['layout']['tablet']['landscape']['tablet-landscape-sidebar-width-wrapper']['tablet-landscape-sidebar-message-wrapper']['message'] = array(
    '#markup' => t('<div class="description">In this layout <em>Second sidebar</em> wraps below.</div>'),
  );

  // Page width
  $form['layout']['tablet']['landscape']['tablet-landscape-page-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set the page width'),
    '#description' => t('<strong>Set the page width</strong>'),
  );

  // Unit
  $form['layout']['tablet']['landscape']['tablet-landscape-page-width-wrapper']['tablet_landscape_page_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('tablet_landscape_page_unit'),
    '#options' => $unit_options,
  );

  // Width
  $form['layout']['tablet']['landscape']['tablet-landscape-page-width-wrapper']['tablet_landscape_page_width'] = array(
    '#type'  => 'textfield',
    '#title' => t('Page width'),
    '#default_value' => Html::escape(theme_get_setting('tablet_landscape_page_width')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#required' => TRUE,
  );

  // Media Queries
  $form['layout']['tablet']['landscape']['tablet-landscape-media-queries-wrapper'] = array(

    '#title' => t('Tablet Landscape Media Queries'),
    '#weight' => 1,
    '#attributes' => array(
      'class' => array(
        'at-media-queries',
      ),
    ),
  );

  // Media query
  $form['layout']['tablet']['landscape']['tablet-landscape-media-queries-wrapper']['tablet_landscape_media_query'] = array(
    '#type' => 'textfield',
    '#title' => t('Media query for this layout'),
    '#default_value' => Html::escape(theme_get_setting('tablet_landscape_media_query')),
    '#description' => t('Do not include @media, it\'s included automatically.'),
    '#size' => 100,
    '#required' => TRUE,
  );


  /* *****************
   * Tablet portrait
   * *************** */

  $form['layout']['tablet']['portrait'] = array(
    '#type' => 'details',
    '#title' => t('Portrait'),
    '#description' => t('<h4>Portrait tablet <span class="field-description-info">(narrow)</span></h4>'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  // Tablet portrait Layout options
  $form['layout']['tablet']['portrait']['tablet-portrait-layout-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Choose sidebar layout'),
  );

  // Options
  $form['layout']['tablet']['portrait']['tablet-portrait-layout-wrapper']['tablet_portrait_layout'] = array(
    '#type' => 'radios',
    '#title' => t('<strong>Choose sidebar positions</strong>'),
    '#default_value' => str_replace('-', '_', theme_get_setting('tablet_portrait_layout')),
    '#options' => $tablet_portrait_options,
  );

  // Tablet portrait Sidebars
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set sidebar widths'),
    '#description' => t('<strong>Set the width of each sidebar</strong>'),
    '#states' => array(
      'invisible' => array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_stack')),
    ),
  );

  // Units
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper']['tablet_portrait_sidebar_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('tablet_portrait_sidebar_unit'),
    '#options' => $unit_options,
  );

  // Sidebar first
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper']['tablet_portrait_sidebar_first'] = array(
    '#type' => 'textfield',
    '#title' => t('First sidebar'),
    '#default_value' => Html::escape(theme_get_setting('tablet_portrait_sidebar_first')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'invisible' => array(
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_stack')),
      ),
      'required' => array(
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_vert')),
        array('input[name="tablet_portrait_layout"]' => array('value' => 'two_sidebars_left_stack')),
        array('input[name="tablet_portrait_layout"]' => array('value' => 'two_sidebars_right_stack')),
      ),
    ),
  );

  // Sidebar second
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper']['tablet_portrait_sidebar_second'] = array(
    '#type' => 'textfield',
    '#title' => t('Second sidebar'),
    '#default_value' => Html::escape(theme_get_setting('tablet_portrait_sidebar_second')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'invisible' => array(
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_stack')),
        array('input[name="tablet_portrait_layout"]' => array('value' => 'two_sidebars_left_stack')),
        array('input[name="tablet_portrait_layout"]' => array('value' => 'two_sidebars_right_stack')),
      ),
      'required' => array(
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_vert')),
      ),
    ),
  );

  // Conditional messages for sidebar layouts
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper']['tablet-portrait-sidebar-message-wrapper'] = array(
    '#type' => 'details',
    '#states' => array(
      'invisible' => array(
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_vert')),
        array('input[name="tablet_portrait_layout"]' => array('value' => 'one_col_stack')),
      ),
    ),
  );
  $form['layout']['tablet']['portrait']['tablet-portrait-sidebar-width-wrapper']['tablet-portrait-sidebar-message-wrapper']['message'] = array(
    '#markup' => t('<div class="description">In this layout <em>Second sidebar</em> wraps below.</div>'),
  );

  // Tablet portrait Page width
  $form['layout']['tablet']['portrait']['tablet-portrait-page-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set the page width'),
    '#description' => t('<strong>Set the page width</strong>'),
  );

  // Unit
  $form['layout']['tablet']['portrait']['tablet-portrait-page-width-wrapper']['tablet_portrait_page_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('tablet_portrait_page_unit'),
    '#options' => $unit_options,
  );

  // Width
  $form['layout']['tablet']['portrait']['tablet-portrait-page-width-wrapper']['tablet_portrait_page_width'] = array(
    '#type'  => 'textfield',
    '#title' => t('Page width'),
    '#default_value' => Html::escape(theme_get_setting('tablet_portrait_page_width')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#required' => TRUE,
  );

  // Tablet portrait Media queries
  $form['layout']['tablet']['portrait']['tablet-portrait-media-queries-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Tablet Portrait Media Queries'),
    //'#weight' => 1,
    '#attributes' => array(
      'class' => array('at-media-queries'),
    ),
  );

  // Media query
  $form['layout']['tablet']['portrait']['tablet-portrait-media-queries-wrapper']['tablet_portrait_media_query'] = array(
    '#type' => 'textfield',
    '#title' => t('Media query for this layout'),
    '#default_value' => Html::escape(theme_get_setting('tablet_portrait_media_query')),
    '#description' => t('Do not include @media, it\'s included automatically.'),
    '#size' => 100,
    '#required' => TRUE,
  );
  /* ****************************************************************************
   *
   * Smalltouch
   *
   * ************************************************************************** */

  $form['layout']['smalltouch'] = array(
    '#type' => 'details',
    '#title' => t('Smalltouch Sidebar Layout'),
    '#description' => t('<h3>Smalltouch Layout</h3><p>Smalltouch devices such as iPhone, Android and Windows phones have two orientations - landscape and portrait, which can also be thought of as wide and arrow smalltouch devices. You can configure a layout for landscape orientation only - portrait orientation (narrow) will always display in one column (all regions full width and stacked) with sidebars below the main content.</p>'),
    '#attributes' => array(
      'class' => array('mayo-layout-form'),
    ),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  /* **********************
   * Smalltouch landscape
   * ******************** */

  $form['layout']['smalltouch']['landscape'] = array(
    '#type' => 'details',
    '#title' => t('Landscape'),
    '#description' => t('<h4>Landscape smalltouch <span class="field-description-info">(wide)</span></h4>'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-layout-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Choose sidebar layout'),
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-layout-wrapper']['smalltouch_landscape_layout'] = array(
    '#type' => 'radios',
    '#title' => t('<strong>Choose sidebar positions</strong>'),
    '#default_value' => theme_get_setting('smalltouch_landscape_layout') ? str_replace('-', '_', theme_get_setting('smalltouch_landscape_layout')) : str_replace('-', '_', theme_get_setting('smartphone_landscape_layout')),
    '#options' => $smalltouch_landscape_options,
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-sidebar-width-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Set sidebar widths'),
    '#description' => t('<strong>Set the width of each sidebar</strong>'),
    '#states' => array(
      '!visible' => array('input[name="smalltouch_landscape_layout"]' => array('value' => 'one_col_stack')),
    ),
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-sidebar-width-wrapper']['smalltouch_landscape_sidebar_unit'] = array(
    '#type' => 'select',
    '#title' => t('Unit'),
    '#default_value' => theme_get_setting('smalltouch_landscape_sidebar_unit') ? theme_get_setting('smalltouch_landscape_sidebar_unit') : theme_get_setting('smartphone_landscape_sidebar_unit'),
    '#options' => $unit_options,
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-sidebar-width-wrapper']['smalltouch_landscape_sidebar_first'] = array(
    '#type' => 'textfield',
    '#title' => t('First sidebar'),
    '#default_value' => theme_get_setting('smalltouch_landscape_sidebar_first') ? Html::escape(theme_get_setting('smalltouch_landscape_sidebar_first')) : Html::escape(theme_get_setting('smartphone_landscape_sidebar_first')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'required' => array('input[name="smalltouch_landscape_layout"]' => array('value' => 'one_col_vert')),
    ),
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-sidebar-width-wrapper']['smalltouch_landscape_sidebar_second'] = array(
    '#type' => 'textfield',
    '#title' => t('Second sidebar'),
    '#default_value' => theme_get_setting('smalltouch_landscape_sidebar_second') ? Html::escape(theme_get_setting('smalltouch_landscape_sidebar_second')) : Html::escape(theme_get_setting('smartphone_landscape_sidebar_second')),
    '#size' => 4,
    '#maxlenght' => 4,
    '#states' => array(
      'required' => array('input[name="smalltouch_landscape_layout"]' => array('value' => 'one_col_vert')),
    ),
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-media-queries-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Smalltouch Landscape Media Queries'),
    '#weight' => 1,
    '#attributes' => array(
      'class' => array('at-media-queries'),
    ),
  );

  $form['layout']['smalltouch']['landscape']['smalltouch-landscape-media-queries-wrapper']['smalltouch_landscape_media_query'] = array(
    '#type' => 'textfield',
    '#title' => t('Media query for this layout'),
    '#default_value' => theme_get_setting('smalltouch_landscape_media_query') ? Html::escape(theme_get_setting('smalltouch_landscape_media_query')) : Html::escape(theme_get_setting('smartphone_landscape_media_query')),
    '#description' => t('Do not include @media, it\'s included automatically.'),
    '#size' => 100,
    //'#required' => TRUE,
  );

  // Pass hidden values to the sumbit function, these values are required but the user can't change them via the UI
  $form['layout']['smalltouch']['landscape']['hidden']['smalltouch_landscape_page_width'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_landscape_page_width') ? Html::escape(theme_get_setting('smalltouch_landscape_page_width')) : Html::escape(theme_get_setting('smartphone_landscape_page_width')),
  );
  $form['layout']['smalltouch']['landscape']['hidden']['smalltouch_landscape_page_unit'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_landscape_page_unit') ? theme_get_setting('smalltouch_landscape_page_unit') : theme_get_setting('smartphone_landscape_page_unit'),
  );

  /* *********************
   * Smalltouch portrait
   * ******************* */

  $form['layout']['smalltouch']['portrait'] = array(
    '#type' => 'details',
    '#title' => t('Portrait'),
    '#description' => t('<h4>Portrait smalltouch <span class="field-description-info">(narrow)</span></h4><div class="smalltouch-portrait-layout">One column</div><p>The smalltouch portrait layout always displays in one column with sidebars stacked horizontally below the main content. All widths are always 100%.</p>'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );

  $form['layout']['smalltouch']['portrait']['smalltouch-portrait-media-queries-wrapper'] = array(
    '#type' => 'details',
    '#title' => t('Smalltouch Portrait Media Queries'),
    '#weight' => 1,
    '#attributes' => array(
      'class' => array('at-media-queries'),
    ),
  );

  $form['layout']['smalltouch']['portrait']['smalltouch-portrait-media-queries-wrapper']['smalltouch_portrait_media_query'] = array(
    '#type' => 'textfield',
    '#title' => t('Media query for this layout'),
    '#default_value' => theme_get_setting('smalltouch_portrait_media_query') ? Html::escape(theme_get_setting('smalltouch_portrait_media_query')) : Html::escape(theme_get_setting('smartphone_portrait_media_query')),
    '#description' => t('Do not include @media, it\'s included automatically.'),
    '#size' => 100,
  );

  // Pass hidden values to the sumbit function, these values are required but the user can't change them via the UI
  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_page_width'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_page_width') ? Html::escape(theme_get_setting('smalltouch_portrait_page_width')) : Html::escape(theme_get_setting('smartphone_portrait_page_width')),
  );

  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_page_unit'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_page_unit') ? theme_get_setting('smalltouch_portrait_page_unit') : theme_get_setting('smartphone_portrait_page_unit'),
  );

  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_sidebar_first'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_sidebar_first') ? Html::escape(theme_get_setting('smalltouch_portrait_sidebar_first')) : Html::escape(theme_get_setting('smartphone_portrait_sidebar_first')),
  );

  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_sidebar_second'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_sidebar_second') ? Html::escape(theme_get_setting('smalltouch_portrait_sidebar_second')) : Html::escape(theme_get_setting('smartphone_portrait_sidebar_second')),
  );

  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_sidebar_unit'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_sidebar_unit') ? Html::escape(theme_get_setting('smalltouch_portrait_sidebar_unit')) : Html::escape(theme_get_setting('smartphone_portrait_sidebar_unit')),
  );

  $form['layout']['smalltouch']['portrait']['hidden']['smalltouch_portrait_layout'] = array(
    '#type' => 'hidden',
    '#default_value' => theme_get_setting('smalltouch_portrait_layout') ? str_replace('-', '_', theme_get_setting('smalltouch_portrait_layout')) : str_replace('-', '_', theme_get_setting('smartphone_portrait_layout')),
  );

   /* --------------- Style settings -------------- */
  $form['style'] = array(
    '#type' => 'details',
    '#title' => t('Style settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['style']['round_corners'] = array(
    '#type' => 'select',
    '#title' => t('Content box round corners'),
    '#default_value' => theme_get_setting('round_corners'),
    '#description' => t('Make the corner of sidebar block and/or node rounded.'),
    '#options' => array(
      'rc-0' => t('No round corners'),
      'rc-1' => t('Sidebar block only'),
      'rc-2' => t('Node only'),
      'rc-3' => t('Both sidebar block and node'),
    ),
    '#suffix' => '<img src="' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/round-corners.png')->toString() . '" /><br />',
  );

  $form['style']['menubar_style'] = array(
    '#type' => 'radios',
    '#title' => t('Menubar style'),
    '#default_value' => theme_get_setting('menubar_style'),
    '#options' => array(
      1 => t('1. Normal (based on the colors specified by the color set)'),
      2 => t('2. Gloss black image background.'),
    ),
    '#suffix' => '<img src="' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/menubar-type.png')->toString() . '" />',
  );
  $form['style']['note'] = array(
    '#type' => 'item',
    '#title' => t('Note:'),
    '#markup' => t('When the menubar type 2 is selected, the menu text color, menu highlight color, menu divier color from the color set are ignored and the fixed colors that match to the menubar are used instead.  Besides, highlight color and menu divider color from the color set are still used for other places such as tabs and sub-menubar for superfish and nice_menus menu.'),
  );

  $form['style']['menubar_background'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow Menubar background color.'),
    '#default_value' => theme_get_setting('menubar_background'),
    '#description' => t('Add your own hex background color below.'),
  );

  $form['style']['menubar_bg_value'] = array(
    '#type' => 'textfield',
    '#title' => t('Meubar background color'),
    '#default_value' => theme_get_setting('menubar_bg_value'),
    '#size' => 7,
    '#maxlength' => 7,
    '#description' => t('Specify the background color for the menubar. This setting is used only when the <em>Allow Meubar background</em> option is checked above.'),
  );

  /* --------------- Advanced header settings -------------- */
  $form['adv_header'] = array(
    '#type' => 'details',
    '#title' => t('Advanced header settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['adv_header']['header_searchbox'] = array(
    '#type' => 'checkbox',
    '#title' => t('Add search form to the header'),
    '#default_value' => theme_get_setting('header_searchbox'),
    '#description' => t('Check here if you want to add search form block to the right side of the header. Search module must be enabled for this to work.'),
  );
  $form['adv_header']['header_fontsizer'] = array(
    '#type' => 'checkbox',
    '#title' => t('Add font resizing controls'),
    '#default_value' => theme_get_setting('header_fontsizer'),
    '#description' => t('Check here if you want to add font resizing controls at side of the header.'),
  );
  $form['adv_header']['header_border_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Header border width'),
    '#default_value' => theme_get_setting('header_border_width'),
    '#size' => 12,
    '#maxlength' => 8,
    '#description' => t('Specify the header border width in px. Note that header border is not drawn when you use header background image or when you use layout style 2.'),
  );
  $form['adv_header']['searchbox_size'] = array(
    '#type' => 'textfield',
    '#title' => t('Search form textfield width'),
    '#default_value' => theme_get_setting('searchbox_size'),
    '#size' => 10,
    '#maxlength' => 6,
    '#description' => t('Specify the width of the text field of the search forms in characters. This size is also applied for the search form in a block. NOTE: do not add px since this is not px size.'),
  );
  $form['adv_header']['header_bg_file'] = array(
    '#type' => 'textfield',
    '#title' => t('URL of the header background image'),
    '#default_value' => theme_get_setting('header_bg_file'),
    '#description' => t('Enter a URL of the form (/sites/default/files/your-background.jpg). If the background image is bigger than the header area, it is clipped. If it\'s smaller than the header area, it is tiled to fill the header area. To remove the background image, blank this field and save the settings.'),
    '#size' => 40,
    '#maxlength' => 120,
  );
  $form['adv_header']['header_bg'] = array(
    '#type' => 'file',
    '#title' => t('Upload header background image'),
    '#size' => 40,
    '#attributes' => array('enctype' => 'multipart/form-data'),
    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your header background image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
    '#element_validate' => array('mayo_header_bg_validate'),
  );
  $form['adv_header']['header_bg_alignment'] = array(
    '#type' => 'select',
    '#title' => t('Header backgeround image alignment'),
    '#default_value' => theme_get_setting('header_bg_alignment'),
    '#description' => t('Select the alignment of the header background image.'),
    '#options' => array(
      'top left' => t('Top left'),
      'top center' => t('Top center'),
      'top right' => t('Top right'),
      'center left' => t('Center left'),
      'center center' => t('Center center'),
      'center right' => t('Center right'),
      'bottom left' => t('Bottom left'),
      'bottom center' => t('Bottom center'),
      'bottom right' => t('Bottom right'),
    ),
  );
  $form['adv_header']['header_watermark'] = array(
    '#type' => 'select',
    '#title' => t('Header watermark'),
    '#default_value' => theme_get_setting('header_watermark'),
    '#description' => t('Select the watermark you want from the list below. The sample below is scaled down and the actual size of the watermark is bigger.'),
    '#options' => array(
      0 => t('-None-'),
      1 => t('Pixture'),
      2 => t('Wave'),
      3 => t('Bubble'),
      4 => t('Flower'),
      5 => t('Star'),
      6 => t('Metal'),
    ),
    '#suffix' => '<img src="' . Url::fromUri('base:' . drupal_get_path('theme', 'mayo') . '/images/watermark-sample.png')->toString() . '" /><br />',
  );

  /* --------------- Misellanenous settings -------------- */
  $form['misc'] = array(
    '#type' => 'details',
    '#title' => t('Miscellaneous settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['misc']['dark_messages'] = array(
    '#type' => 'checkbox',
    '#title' => t('Use dark message colors'),
    '#default_value' => theme_get_setting('dark_messages'),
    '#return_value' => 'dark-messages',
    '#description' => t('Check here if you use the dark color set. Colors for the status/warning/error messages are adjusted.'),
  );

  /*
   * Originally posted by dvessel (http://drupal.org/user/56782).
   * The following will be processed even if the theme is inactive.
   * If you are on a theme specific settings page but it is not an active
   * theme (example.com/admin/apearance/settings/THEME_NAME), it will
   * still be processed.
   *
   * Build a list of themes related to the theme specific form. If the form
   * is specific to a sub-theme, all parent themes leading to it will have
   * hook_form_theme_settings invoked. For example, if a theme named
   * 'grandchild' has its settings form in focus, the following will be invoked.
   * - parent_form_theme_settings()
   * - child_form_theme_settings()
   * - grandchild_form_theme_settings()
   *
   * If 'child' was in focus it will invoke:
   * - parent_form_theme_settings()
   * - child_form_theme_settings()
   *
   *  @see http://drupal.org/node/943212
   */
  $form_themes = array();
  $theme_handler = \Drupal::service('theme_handler');
  $themes = $theme_handler->listInfo();  // Get a list of available themes.
  $_theme = \Drupal::theme()->getActiveTheme()->getName();
  while (isset($_theme)) {
    $form_themes[$_theme] = $_theme;
    $_theme = isset($themes[$_theme]->base_theme) ? $themes[$_theme]->base_theme : NULL;
  }
  $form_themes = array_reverse($form_themes);

  foreach ($form_themes as $theme_key) {
    if (function_exists($form_settings = "{$theme_key}_form_theme_settings")) {
      $form_settings($form, $form_state);
    }
  }

  // Include custom form validation and submit functions
  require_once(drupal_get_path('theme', 'mayo') . '/inc/forms/mayo.validate.inc');
  require_once(drupal_get_path('theme', 'mayo') . '/inc/forms/mayo.submit.inc');

  // Custom validate and submit functions
  // Custom submit buggy. Edited FormBuilder.php with patch in #12
  // found here: https://drupal.org/node/2252165
  $form['#validate'][] = 'mayo_settings_validate';
  $form['#submit'][] = 'mayo_settings_submit';
}

/**
 * Check and save the uploaded header background image
 */
function mayo_header_bg_validate($element, FormStateInterface $form_state) {
  global $base_url;

  $validators = array('file_validate_extensions' => array('png gif jpg jpeg apng svg'));
  $file = file_save_upload('header_bg', $validators, "public://", NULL, FILE_EXISTS_REPLACE);

  if (!empty($file)) {
    // change file's status from temporary to permanent and update file database
    if ((is_object($file[0]) == 1)) {
      $file[0]->status = FILE_STATUS_PERMANENT;
      $file[0]->save();
      $uri = $file[0]->getFileUri();
      $file_url = file_create_url($uri);
      $file_url = str_ireplace($base_url, '', $file_url);
      $form_state->setValue('header_bg_file', $file_url);
    }
 }
}
