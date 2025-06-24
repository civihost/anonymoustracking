<?php

require_once 'anonymoustracking.civix.php';

use CRM_Anonymoustracking_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function anonymoustracking_civicrm_config(&$config): void
{
  _anonymoustracking_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function anonymoustracking_civicrm_install(): void
{
  _anonymoustracking_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function anonymoustracking_civicrm_enable(): void
{
  _anonymoustracking_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_pre().
 */
function anonymoustracking_civicrm_pre($op, $objectName, $id, &$params)
{
  if ($objectName === 'MailingEventTrackableURLOpen' && $op === 'create') {
    CRM_Anonymoustracking_Mailing_Page_Url::run();
  } elseif ($objectName === 'MailingEventOpened' && $op === 'create') {
  } elseif ($objectName === 'Mailing' && $op === 'edit') {

    $anonymous_click_tracking = $params['anonymous_click_tracking'] ?? NULL;
    if (is_null($anonymous_click_tracking)) {
      return;
    }

    $customFieldId = CRM_Anonymoustracking_Utils::getMailingCustomFieldId();
    if ($customFieldId) {
      $customParams = [
        'entityID' => $id,
        'custom_' . $customFieldId => $anonymous_click_tracking,
      ];
    Civi::log()->debug('civicrm_pre ' . $objectName . ' op: ' . $op . ' params: ' . print_r($anonymous_click_tracking, true) . ' customfield: '.  print_r($customParams, true) . ' id mailing: ' . $id);
      \CRM_Core_BAO_CustomValueTable::setValues($customParams);
    }
  }
  /*
2025-05-13 09:56:18+0200  [debug] civicrm_pre MailingEventTrackableURLOpen op: create params: Array
(
    [event_queue_id] => 494985
    [trackable_url_id] => 14049
    [time_stamp] => 20250513095618
)


2025-05-13 09:57:25+0200  [debug] civicrm_pre MailingEventOpened op: create params: Array
(
    [event_queue_id] => 493412
    [time_stamp] => 20250513095725
)
*/
}

function anonymoustracking_civicrm_alterAngular(\Civi\Angular\Manager $angular)
{
      //$value = civicrm_api3('Mailing', 'getsingle', [
      //  'id' => $mailingId,
      //  'return' => ['custom_enable_anonymous_tracking_54'],
      //]);
  // TODO how to load the value of custom field in Angular?
  $changeSet = \Civi\Angular\ChangeSet::create('inject_anonymoustracking')
    ->alterHtml('~/crmMailing/BlockTracking.html', function (phpQueryObject $doc) {
      $field = $doc->find('[name=open_tracking]')->parent('[crm-ui-field]');
      $field->after('<div crm-ui-field="{name: \'subform.anonymous_click_tracking\', title: ts(\'Anonymous tracking\')}" crm-layout="checkbox">
      <input crm-ui-id="subform.open_tracking" name="custom_54" type="checkbox" ng-model="mailing.custom_54" ng-true-value="1" ng-false-value="0" />
      </div>');
    });
  $angular->add($changeSet);
}
