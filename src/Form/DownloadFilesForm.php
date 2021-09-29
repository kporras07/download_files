<?php

namespace Drupal\download_files\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\download_files\Event\DownloadFilesEvents;
use Drupal\download_files\Event\FileDownloaded;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Session\AccountProxy;

/**
 * Provides a Download Files form.
 */
class DownloadFilesForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Current User.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Construct a DownloadFilesForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   * @param \Drupal\Core\Session\AccountProxy $currentUser
   *   Current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher, AccountProxy $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->eventDispatcher = $event_dispatcher;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'download_files_download_files';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['file'] = [
      '#type' => 'select',
      '#title' => $this->t('File name'),
      '#required' => TRUE,
      '#description' => $this->t('Select the file that you want to download'),
      '#options' => $this->getOptions(),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download'),
    ];

    return $form;
  }

  /**
   * Build options array for file select.
   */
  public function getOptions() {
    $storage = $this->entityTypeManager->getStorage('file');
    $fids = $storage->getQuery()
      ->condition('status', 1)
      ->execute();

    $documents = $storage->loadMultiple($fids);

    $options = [];
    foreach ($documents as $document) {
      $options[$document->getFileUri()] = $document->getFilename();
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uri = $form_state->getValue('file');
    $uid = $this->currentUser->id();
    $this->messenger()->addStatus($this->t('File to download: @uri', [
      '@uri' => $uri
    ]));
    $response = new BinaryFileResponse($uri);
    $response->setContentDisposition('attachment');

    $event = new FileDownloaded($uri, $uid);
    $this->eventDispatcher->dispatch(DownloadFilesEvents::FILE_DOWNLOADED, $event);

    $form_state->setResponse($response);
  }

}
