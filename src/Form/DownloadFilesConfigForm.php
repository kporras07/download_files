<?php

namespace Drupal\download_files\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Download Files settings for this site.
 */
class DownloadFilesConfigForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Construct a DownloadFilesConfigForm.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->database = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'download_files_download_files_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['download_files.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['file_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('File types to include in the download files form'),
      '#default_value' => $this->config('download_files.settings')->get('file_types'),
      '#options' => $this->getFileTypes(),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  public function getFileTypes() {
    // @todo: Improve this to get a nice label.
    // @todo Use DI.
    $query = $this->database
      ->select('file_managed', 'f')
      ->fields('f', ['filemime'])
      ->distinct()
      ->execute()
      ->fetchAll();

    $types = [];
    foreach ($query as $file) {
      $label_parts = explode('/', $file->filemime);
      $label = end($label_parts);
      $types[$file->filemime] = strtoupper($label);
    }
    return $types;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('download_files.settings')
      ->set('file_types', $form_state->getValue('file_types'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
