<?php

namespace Drupal\mymodule\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class mymoduleSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymodule_admin_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mymodule.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mymodule.config');

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('title'),
    ];

    $form['desc'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('desc'),
    ];

    $form['allow_desc'] = [
      '#type' => 'radios',
      '#title' => t('Allow Description'),
      '#default_value' => $config->get('allow_desc'),
      '#options' => [
        t('No'),
        t('Yes'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
    \Drupal::configFactory()->getEditable('mymodule.config')
      // Set the submitted configuration setting
      ->set('title', $form_state->getValue('title'))
      // You can set multiple configurations at once by making
      // multiple calls to set()
      ->set('desc', $form_state->getValue('desc'))
      ->set('allow_desc', $form_state->getValue('allow_desc'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}