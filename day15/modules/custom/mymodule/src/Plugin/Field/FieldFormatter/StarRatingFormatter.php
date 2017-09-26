<?php

/**
 * @file
 * Contains \Drupal\mymodule\Plugin\Field\FieldFormatter\StarRatingFormatter.
 */

namespace Drupal\mymodule\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'star_rating_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "star_rating_formatter",
 *   label = @Translation("Star rating formatter"),
 *   field_types = {
 *     "decimal"
 *   }
 * )
 */
class StarRatingFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#theme' => 'star_rating',
        '#ratingvalue' => $this->viewValue($item) * 20,
        '#attached' => array('library'=> array('star_rating/star_rating')),
      );
    }

    return $elements;
  }

}
