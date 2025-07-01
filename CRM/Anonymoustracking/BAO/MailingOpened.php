<?php
/**
 * Adapted from CiviCRM core: CRM_Mailing_Event_BAO_MailingEventOpened (CRM/Mailing/Event/BAO/MailingEventOpened.php)
 *
 * Modified by Samuele Masetto, as part of an anonymous click-tracking feature.
 *
 * This code is licensed under the AGPLv3: https://www.gnu.org/licenses/agpl-3.0.html
 */

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_BAO_MailingOpened extends CRM_Anonymoustracking_DAO_MailingOpened
{
  /**
   * Register an open event.
   *
   * @param int $mailing_id
   *   The Mailing ID of the recipient.
   * @param int $queue_id
   *   The Queue Event ID of the recipient.
   *
   * @return bool
   */
  public static function open($mailing_id, $queue_id)
  {
    // First make sure there's a matching queue event.
    $q = new CRM_Mailing_Event_BAO_MailingEventQueue();
    $q->id = $queue_id;
    if ($q->find(TRUE)) {
      self::writeRecord([
        'mailing_id' => $mailing_id,
        'anonymous_id' => CRM_Anonymoustracking_Utils::getAnonymizedQueueId($queue_id),
        'time_stamp' => date('YmdHis'),
      ]);
      return TRUE;
    }

    return FALSE;
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
    $toDate = NULL
  ) {
    $dao = new CRM_Core_DAO();

    $open = self::getTableName();
    $mailing = CRM_Mailing_BAO_Mailing::getTableName();

    $distinct = NULL;
    if ($is_distinct) {
      $distinct = 'DISTINCT ';
    }
    $query = "
            SELECT      COUNT($distinct $open.anonymous_id) as opened
            FROM        $open
            INNER JOIN  $mailing
                    ON  $open.mailing_id = $mailing.id
            WHERE       $mailing.id = " . CRM_Utils_Type::escape($mailing_id, 'Integer');

    if (!empty($toDate)) {
      $query .= " AND $open.time_stamp <= $toDate";
    }

    $dao->query($query);

    if ($dao->fetch()) {
      return $dao->opened;
    }

    return NULL;
  }

  /**
   * @see https://issues.civicrm.org/jira/browse/CRM-12814
   * Get opened count for each mailing for a given set of mailing IDs
   *
   * @param int[] $mailingIDs
   *
   * @return array
   *   Opened count per mailing ID
   */
  public static function getMailingTotalCount($mailingIDs) {
    $dao = new CRM_Core_DAO();
    $openedCount = [];

    $open = self::getTableName();
    $mailing = CRM_Mailing_BAO_Mailing::getTableName();
    $mailingIDs = implode(',', $mailingIDs);

    $query = "
      SELECT $open.mailing_id as mailingID, COUNT($open.id) as opened
      FROM $open
      INNER JOIN $mailing
              ON $open.mailing_id = $mailing.id
      WHERE $open.mailing_id IN ({$mailingIDs})
      GROUP BY $open.mailing_id
    ";

    $dao->query($query);

    while ($dao->fetch()) {
      $openedCount[$dao->mailingID] = $dao->opened;
    }
    return $openedCount;
  }

}
