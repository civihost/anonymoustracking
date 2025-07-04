<?php

/**
 * Adapted from CiviCRM core: CRM_Mailing_Page_Url (CRM/Mailing/Page/Url.php)
 * https://github.com/civicrm/civicrm-core/blob/master/CRM/Mailing/Page/Url.php
 *
 * Modified by Samuele Masetto, as part of an anonymous click-tracking feature.
 *
 * This code is licensed under the AGPLv3: https://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Anonymoustracking_Mailing_Page_Url
{

  public static function run()
  {
    $queue_id = CRM_Utils_Request::retrieveValue('qid', 'Integer');
    $url_id = CRM_Utils_Request::retrieveValue('u', 'Integer', NULL, TRUE);

    $mailing_id = CRM_Anonymoustracking_Utils_Mailings::getMailingIdFromQueueId($queue_id);
    if (!$mailing_id) {
      return;
    }

    $anonymous_tracking = CRM_Anonymoustracking_Utils_Mailings::getAnonyousTrackingFromMailingId($mailing_id);
    if (!$anonymous_tracking) {
      return;
    }

    $url = trim(CRM_Anonymoustracking_BAO_MailingUrlOpen::track($mailing_id, $queue_id, $url_id));

    if (!$url) {
      return;
    }

    $query_string = self::ExtractPassthroughParameters();

    if (strlen($query_string) > 0) {
      // Parse the url to preserve the fragment.
      $pieces = parse_url($url);

      if (isset($pieces['fragment'])) {
        $url = str_replace('#' . $pieces['fragment'], '', $url);
      }

      // Handle additional query string params.
      if ($query_string) {
        if (stristr($url, '?')) {
          $url .= '&' . $query_string;
        } else {
          $url .= '?' . $query_string;
        }
      }

      // slap the fragment onto the end per URL spec
      if (isset($pieces['fragment'])) {
        $url .= '#' . $pieces['fragment'];
      }
    }

    CRM_Utils_System::redirect($url, [
      'for' => 'civicrm/mailing/url',
      'queue_id' => $queue_id,
      'url_id' => $url_id,
      'noindex' => TRUE,
    ]);
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
