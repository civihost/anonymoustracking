<?php

/**
 * Adapted from CiviCRM core: CRM_Mailing_Page_Open (CRM/Mailing/Page/Open.php)
 * https://github.com/civicrm/civicrm-core/blob/master/CRM/Mailing/Page/Open.php
 *
 * Modified by Samuele Masetto, as part of an anonymous click-tracking feature.
 *
 * This code is licensed under the AGPLv3: https://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Anonymoustracking_Mailing_Page_Open
{

  public static function run()
  {
    $queue_id = CRM_Utils_Request::retrieveValue('qid', 'Positive', NULL, FALSE, 'GET');
    if (!$queue_id) {
      CRM_Utils_System::sendInvalidRequestResponse(ts("Missing input parameters"));
    }

    $mailing_id = CRM_Anonymoustracking_Utils::getMailingIdFromQueueId($queue_id);
    if (!$mailing_id) {
      return;
    }

    $anonymous_tracking = CRM_Anonymoustracking_Utils::getAnonyousTrackingFromMailingId($mailing_id);
    if (!$anonymous_tracking) {
      return;
    }

    CRM_Anonymoustracking_BAO_MailingOpened::open($mailing_id, $queue_id);

    $filename = Civi::paths()->getPath('[civicrm.root]/i/tracker.gif');

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-type: image/gif');
    header('Content-Length: ' . filesize($filename));
    header('Content-Disposition: inline; filename=tracker.gif');

    readfile($filename);

    CRM_Utils_System::civiExit();
  }

  /**
   * This function is copied verbatim from CRM_Mailing_Page_Url::ExtractPassthroughParameters()
   * https://github.com/civicrm/civicrm-core/blob/master/CRM/Mailing/Page/Url.php
   *
   * @return string
   */
  protected static function extractPassthroughParameters(): string
  {
    $config = CRM_Core_Config::singleton();

    $query_param = $_GET;
    unset($query_param['qid']);
    unset($query_param['u']);
    unset($query_param[$config->userFrameworkURLVar]);

    // @see dev/core#1865 for some additional query strings we need to remove as well.
    if ($config->userFramework === 'WordPress') {
      // Ugh
      unset($query_param['page']);
      unset($query_param['noheader']);
      unset($query_param['civiwp']);
    } elseif ($config->userFramework === 'Joomla') {
      unset($query_param['option']);
    }

    $query_string = http_build_query($query_param);
    return $query_string;
  }
}
