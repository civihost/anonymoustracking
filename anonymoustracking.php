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

    // TODO
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
  $changeSet = \Civi\Angular\ChangeSet::create('inject_anonymoustracking')
    ->alterHtml('~/crmMailing/BlockTracking.html', function (phpQueryObject $doc) {
      $field = $doc->find('[name=open_tracking]')->parent('[crm-ui-field]');

      $field->after('<div crm-ui-field="{name: \'subform.anonymous_click_tracking\', title: ts(\'Anonymous tracking\')}"
                    crm-layout="checkbox" anonymous-tracking-init>
  <input crm-ui-id="subform.anonymous_tracking"
         ng-attr-name="anonymous_tracking_field"
         type="checkbox"
         ng-model="mailing[anonymous_tracking_field]"
         ng-true-value="\'1\'"
         ng-false-value="\'0\'" />
</div>');
    });
  $angular->add($changeSet);
}
