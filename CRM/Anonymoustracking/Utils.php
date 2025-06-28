<?php

/**
 * Utils - class with generic functions CiviRules
 */
class CRM_Anonymoustracking_Utils
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
      'anonymous_tracking_default' => 1,//Civi::settings()->get('anonymous_tracking_default'),
    ];
  }
}
