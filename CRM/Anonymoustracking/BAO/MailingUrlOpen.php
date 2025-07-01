<?php
/**
 * Adapted from CiviCRM core: CRM_Mailing_Event_BAO_MailingEventTrackableURLOpen (CRM/Mailing/Event/BAO/MailingEventTrackableURLOpen.php)
 *
 * Modified by Samuele Masetto, as part of an anonymous click-tracking feature.
 *
 * This code is licensed under the AGPLv3: https://www.gnu.org/licenses/agpl-3.0.html
 */

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_BAO_MailingUrlOpen extends CRM_Anonymoustracking_DAO_MailingUrlOpen {

  /**
   * Track a click-through and return the URL to redirect.
   *
   * If the numbers don't match up, return the base url.
   *
   * @param int $mailing_id
   *   The Mailing ID of the clicker.
   * @param int $queue_id
   *   The Queue Event ID of the clicker.
   * @param int $url_id
   *   The ID of the trackable URL.
   *
   * @return string
   *   The redirection url, or base url on failure.
   */
  public static function track($mailing_id,$queue_id, $url_id)
  {
    // To find the url, we also join on the queue and job tables.  This
    // prevents foreign key violations.
    $job = CRM_Utils_Type::escape(CRM_Mailing_BAO_MailingJob::getTableName(), 'MysqlColumnNameOrAlias');
    $eq = CRM_Utils_Type::escape(CRM_Mailing_Event_BAO_MailingEventQueue::getTableName(), 'MysqlColumnNameOrAlias');
    $turl = CRM_Utils_Type::escape(CRM_Mailing_BAO_MailingTrackableURL::getTableName(), 'MysqlColumnNameOrAlias');

    if (!$queue_id) {
      $search = CRM_Core_DAO::executeQuery(
        "SELECT url
           FROM $turl
          WHERE $turl.id = %1",
        [
          1 => [$url_id, 'Integer'],
        ]
      );

      if (!$search->fetch()) {
        return CRM_Utils_System::baseURL();
      }

      return $search->url;
    }

    $search = CRM_Core_DAO::executeQuery(
      "SELECT $turl.url as url
         FROM $turl
        INNER JOIN $job ON $turl.mailing_id = $job.mailing_id
        INNER JOIN $eq ON $job.id = $eq.job_id
        WHERE $eq.id = %1 AND $turl.id = %2",
      [
        1 => [$queue_id, 'Integer'],
        2 => [$url_id, 'Integer'],
      ]
    );

    if (!$search->fetch()) {
      // Can't find either the URL or the queue. If we can find the URL then
      // return the URL without tracking.  Otherwise return the base URL.
      $search = CRM_Core_DAO::executeQuery(
        "SELECT $turl.url as url
           FROM $turl
          WHERE $turl.id = %1",
        [
          1 => [$url_id, 'Integer'],
        ]
      );

      if (!$search->fetch()) {
        return CRM_Utils_System::baseURL();
      }

      return $search->url;
    }

    self::writeRecord([
      'mailing_id' => $mailing_id,
      'anonymous_id' => CRM_Anonymoustracking_Utils::getAnonymizedQueueId($queue_id),
      'trackable_url_id' => $url_id,
      'time_stamp' => date('YmdHis'),
    ]);

    return $search->url;
  }

  /**
   * Get row count for the event selector.
   *
   * @param int $mailing_id
   *   ID of the mailing.
   * @param int $job_id
   *   Optional ID of a job to filter on.
   * @param bool $is_distinct
   *   Group by queue ID?.
   * @param int $url_id
   *   Optional ID of a url to filter on.
   *
   * @param string $toDate
   *
   * @return int
   *   Number of rows in result set
   */
  public static function getTotalCount(
    $mailing_id,
    $job_id = NULL,
    $is_distinct = FALSE,
    $url_id = NULL,
    $toDate = NULL
  ) {
    $dao = new CRM_Core_DAO();

    $click = self::getTableName();
    $mailing = CRM_Mailing_BAO_Mailing::getTableName();

    $distinct = NULL;
    if ($is_distinct) {
      $distinct = 'DISTINCT ';
    }
    $query = "
            SELECT      COUNT($distinct $click.anonymous_id) as opened
            FROM        $click
            INNER JOIN  $mailing
                    ON  $click.mailing_id = $mailing.id
            WHERE       $mailing.id = " . CRM_Utils_Type::escape($mailing_id, 'Integer');

    if (!empty($toDate)) {
      $query .= " AND $click.time_stamp <= $toDate";
    }

    if (!empty($url_id)) {
      $query .= " AND $click.trackable_url_id = " . CRM_Utils_Type::escape($url_id, 'Integer');
    }
Civi::log()->debug($query);
    // query was missing
    $dao->query($query);

    if ($dao->fetch()) {
      return $dao->opened;
    }

    return NULL;
  }

  /**
   * Get tracked url count for each mailing for a given set of mailing IDs.
   *
   * @see https://issues.civicrm.org/jira/browse/CRM-12814
   *
   * @param array $mailingIDs
   *
   * @return array
   *   trackable url count per mailing ID
   */
  public static function getMailingTotalCount($mailingIDs)
  {
    $dao = new CRM_Core_DAO();
    $clickCount = [];

    $click = self::getTableName();
    $mailing = CRM_Mailing_BAO_Mailing::getTableName();
    $mailingIDs = implode(',', $mailingIDs);

    $query = "
      SELECT $click.mailing_id as mailingID, COUNT($click.id) as opened
      FROM $click
      INNER JOIN $mailing
              ON $click.mailing_id = $mailing.id
      WHERE $click.mailing_id IN ({$mailingIDs})
      GROUP BY $click.mailing_id
    ";

    $dao->query($query);

    while ($dao->fetch()) {
      $clickCount[$dao->mailingID] = $dao->opened;
    }
    return $clickCount;
  }

}
