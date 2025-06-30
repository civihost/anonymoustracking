<?php

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
}
