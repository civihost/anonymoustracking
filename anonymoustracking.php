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
    if ($anonymous_click_tracking === NULL || $anonymous_click_tracking === '') {
      return;
    }

    $customFieldId = CRM_Anonymoustracking_Utils::getMailingCustomFieldId();
    if ($customFieldId) {
      $customParams = [
        'entityID' => $id,
        'custom_' . $customFieldId => $anonymous_click_tracking,
      ];
      Civi::log()->debug('civicrm_pre ' . $objectName . ' op: ' . $op . ' params: ' . print_r($anonymous_click_tracking, true) . ' customfield: ' .  print_r($customParams, true) . ' id mailing: ' . $id);
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

function anonymoustracking_civicrm_customPre(string $op, int $groupID, int $entityID, array &$params) {
  // Verifica che sia il gruppo giusto
  // Puoi trovare il group ID dinamicamente o hardcoded se noto
  $myGroupID = 24; // ← ID del gruppo 'anonymoustracking_mailing'

  if ($groupID != $myGroupID) {
    return;
  }
      Civi::log()->debug('anonymoustracking_civicrm_customPre ' . print_r($params, true) );

$params[0]['value']= '1';
  // ID del campo (può essere custom_54)
  //$customFieldKey = 'custom_54';

  //// Se assente o nullo, impostiamo a '0'
  //if (!isset($params[$customFieldKey]) || $params[$customFieldKey] === null) {
  //  $params[$customFieldKey] = '0';
  //}
}


function anonymoustracking_civicrm_alterAngular(\Civi\Angular\Manager $angular)
{
  //$value = civicrm_api3('Mailing', 'getsingle', [
  //  'id' => $mailingId,
  //  'return' => ['custom_enable_anonymous_tracking_54'],
  //]);
  // TODO how to set the value of custom field in Angular?
  $changeSet = \Civi\Angular\ChangeSet::create('inject_anonymoustracking')
    ->alterHtml('~/crmMailing/BlockTracking.html', function (phpQueryObject $doc) {
      $field = $doc->find('[name=open_tracking]')->parent('[crm-ui-field]');
      $field->after('<div crm-ui-field="{name: \'subform.anonymous_click_tracking\', title: ts(\'Anonymous tracking\')}"
                    crm-layout="checkbox"
                    anonymous-tracking-init>
  <input crm-ui-id="subform.anonymous_tracking"
         ng-attr-name="{{settings.anonymous_tracking_field_id}}"
         type="checkbox"
         ng-model="mailing[settings.anonymous_tracking_field_id]"
         ng-true-value="1"
         ng-false-value="0" />
</div>');
      //<input crm-ui-id="subform.open_tracking" name="custom_54" type="checkbox" ng-model="mailing.custom_54" ng-true-value="1" ng-false-value="0" />
    });
  $angular->add($changeSet);
}
