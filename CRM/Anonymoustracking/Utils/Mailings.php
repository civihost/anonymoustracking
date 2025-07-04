<?php

use CRM_Anonymoustracking_ExtensionUtil as E;

class CRM_Anonymoustracking_Utils_Mailings
{
  public static function getAnonymizedQueueId($queueid)
  {
    return hash_hmac('sha256', $queueid, CIVICRM_SITE_KEY);
  }

  public static function getMailingCustomFieldName()
  {
    try {
      $result = civicrm_api3('CustomField', 'getsingle', [
        'name' => 'enable_anonymous_tracking',
        'custom_group_id.name' => 'anonymoustracking_mailing',
      ]);
      return $result['column_name'];
    } catch (Exception $e) {
      return NULL;
    }
  }

  public static function getMailingCustomFieldId()
  {
    try {
      $result = civicrm_api3('CustomField', 'getsingle', [
        'name' => 'enable_anonymous_tracking',
        'custom_group_id.name' => 'anonymoustracking_mailing',
      ]);
      return $result['id'];
    } catch (Exception $e) {
      return NULL;
    }
  }

  public static function getAngularSettings(): array
  {
    return [
      'anonymous_tracking_field_id' => 'custom_' . self::getMailingCustomFieldId(),
      'anonymous_tracking_default' => Civi::settings()->get('anonymous_tracking_default') ? 1 : 0,
    ];
  }

  public static function getMailingIdFromQueueId($queue_id): ?Int
  {
    $q = new CRM_Mailing_Event_BAO_MailingEventQueue();
    $q->id = $queue_id;
    if ($q->find(TRUE)) {
      $mailing = &$q->getMailing();
      return $mailing->id;
    } else {
      return null;
    }
  }

  public static function getAnonyousTrackingFromMailingId($mailing_id): ?Bool
  {
    $custom_field = 'custom_' . self::getMailingCustomFieldId();
    $customParams = [
      'entityID' => $mailing_id,
      $custom_field => 1,
    ];
    $values = \CRM_Core_BAO_CustomValueTable::getValues($customParams);
    if (isset($values[$custom_field])) {
      return (bool) $values[$custom_field];
    }
    return null;
  }
}
