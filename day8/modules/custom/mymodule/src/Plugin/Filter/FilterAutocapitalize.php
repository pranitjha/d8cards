<?php
namespace Drupal\mymodule\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @Filter(
 *   id = "filter_autocapitalize",
 *   title = @Translation("Filter AutoCapitalize"),
 *   description = @Translation("This Filter auto capitalize certain texts."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterAutocapitalize extends FilterBase {
    public function process($text, $langcode) {
        $new_text=$text;
        $texttobecapitalized = $this->settings['autocapitalize_text'] ? $this->settings['autocapitalize_text'] : '';
        $list_of_words = explode(', ', $texttobecapitalized);
        $words = explode(' ', $text);
        foreach($words as $word) {
            if(in_array($word, $list_of_words)) {
                $capitalised_word = ucfirst($word);
                $text = str_replace($word, $capitalised_word, $text);
            }
            $new_text = $text;
        }
        return new FilterProcessResult($new_text);
    }

    public function settingsForm(array $form, FormStateInterface $form_state) {
        $form['autocapitalize_text'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Auto Capitalise Text'),
            '#default_value' => $this->settings['autocapitalize_text'],
            '#description' => $this->t('Input few words that you wish to capitalize separated by comma'),
        );
        return $form;
    }
}
