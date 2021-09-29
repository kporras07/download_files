<?php

namespace Drupal\download_files\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Wraps a incident report event for event subscribers.
 *
 * Whenever there is additional contextual data that you want to provide to the
 * event subscribers when dispatching an event you should create a new class
 * that extends \Symfony\Component\EventDispatcher\Event.
 *
 * See \Drupal\Core\Config\ConfigCrudEvent for an example of this in core.
 *
 * @see \Drupal\Core\Config\ConfigCrudEvent
 *
 * @ingroup events_example
 */
class FileDownloaded extends Event {

  /**
   * File uri.
   *
   * @var string
   */
  protected $fileUri;

  /**
   * User id.
   *
   * @var int
   */
  protected $uid;

  /**
   * Constructs an incident report event object.
   *
   * @param string $file_uri
   *   File uri.
   * @param int $uid
   *   User id that downloaded the file.
   */
  public function __construct($file_uri, $uid) {
    $this->fileUri = $file_uri;
    $this->uid = $uid;
  }

  /**
   * Get the file uri.
   *
   * @return string
   *   The file uri.
   */
  public function getFileUri() {
    return $this->fileUri;
  }

  /**
   * Get the downloader uid.
   *
   * @return string
   *   The downloader uid.
   */
  public function getUid() {
    return $this->uid;
  }

}
