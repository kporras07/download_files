<?php

namespace Drupal\download_files\Event;

/**
 * Defines events for the IncidentReports.
 */
final class DownloadFilesEvents {

  /**
   * Dispatched event when a download is done.
   *
   * @Event
   *
   * @see \Drupal\download_files\Event\FileDownloaded
   *
   * @var string
   */
  const FILE_DOWNLOADED = 'download_files.file_downloaded';
}
