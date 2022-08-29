<?php

namespace Drupal\specbee_location\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure settings for specbee location.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'specbee_location_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['specbee_location.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['country'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Country'),
      '#default_value' => $this->config('specbee_location.settings')->get('country'),
    ];
    $form['city'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('City'),
      '#default_value' => $this->config('specbee_location.settings')->get('city'),
    ];
    $form['site_timezone'] = [
      '#type' => 'select',
      '#title' => t('Time zone'),
      '#default_value' => $this->config('specbee_location.settings')->get('site_timezone'),
      '#options' => [
        'America/Chicago' => t('America/Chicago'),
        'America/New_York'  => t('America/New_York'),
        'Asia/Tokyo'  => t('Asia/Tokyo'),
        'Asia/Dubai'  => t('Asia/Dubai'),
        'Asia/Kolkata'  => t('Asia/Kolkata'),
        'Europe/Amsterdam'  => t('Europe/Amsterdam'),
        'Europe/Oslo'  => t('Europe/Oslo'),
        'Europe/London'  => t('Europe/London'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('specbee_location.settings')
      ->set('country', ucfirst($form_state->getValue('country')))
      ->set('city', ucfirst($form_state->getValue('city')))
      ->set('site_timezone', $form_state->getValue('site_timezone'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
