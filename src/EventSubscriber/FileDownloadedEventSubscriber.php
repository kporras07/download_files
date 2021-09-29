<?php

namespace Drupal\download_files\EventSubscriber;

use Drupal\download_files\Event\FileDownloaded;
use Drupal\download_files\Event\DownloadFilesEvents;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Subscribe to entity delete events and incident reports.
 */
class FileDownloadedEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $manager
   *   Entity type manager.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, EntityTypeManagerInterface $entity_type_manager) {
    $this->logger = $loggerFactory->get('download_files');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[DownloadFilesEvents::FILE_DOWNLOADED][] = ['fileDownloaded'];

    return $events;
  }

  /**
   * Reacts to file downloaded.
   *
   * @param \Drupal\download_files\Event\FileDownloaded $event
   *   The file downloaded event object.
   */
  public function fileDownloaded(FileDownloaded $event) {
    $uri = $event->getFileUri();
    $uid = $event->getUid();
    $user = $this->entityTypeManager->getStorage('user')->load($uid);
    $this->logger->info('The user: ' . $user->label() . ' downloaded the file with uri: ' . $uri);
    $event->stopPropagation();
  }

}
