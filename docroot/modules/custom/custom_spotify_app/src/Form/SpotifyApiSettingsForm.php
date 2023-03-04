<?php

namespace Drupal\custom_spotify_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SpotifyApiSettingsForm.
 *
 * @package Drupal\custom_spotify_app\Form
 */
class SpotifyApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spotify_api_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['spotify_api_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the current configuration values.
    $config = $this->config('custom_spotify_app.settings');

    // Add a fieldset to the form for the Spotify API credentials.
    $form['spotify_api_credentials'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Spotify API credentials'),
    ];

    // Add the Spotify API client ID field.
    $form['spotify_api_credentials']['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client ID'),
      '#description' => $this->t('Enter your Spotify App Client ID.'),
      '#default_value' => $config->get('client_id'),
      '#required' => TRUE,
    ];

    // Add the Spotify API client secret field.
    $form['spotify_api_credentials']['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#description' => $this->t('Enter your Spotify App Client Secret.'),
      '#default_value' => $config->get('client_secret'),
      '#required' => TRUE,
    ];

    // Add a fieldset to the form for the caching settings.
    $form['caching_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Caching settings'),
    ];

    // Add the caching time field.
    $form['caching_settings']['caching_time'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Caching time'),
      '#description' => $this->t('Enter the caching time in seconds.'),
      '#default_value' => $config->get('caching_time'),
      '#required' => TRUE,
    ];

    // Add a select field for the caching backend.
    $form['caching_settings']['caching_backend'] = [
      '#type' => 'select',
      '#title' => $this->t('Caching backend'),
      '#description' => $this->t('Select the caching backend to use.'),
      '#options' => [
        'cache.backend.apcu' => $this->t('APCu'),
        'cache.backend.database' => $this->t('Database'),
        'cache.backend.memcache' => $this->t('Memcached'),
        'cache.backend.memory' => $this->t('Memory'),
      ],
      '#default_value' => $config->get('caching_backend'),
      '#required' => TRUE,
    ];

    // Add a submit button to save the form values.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }
