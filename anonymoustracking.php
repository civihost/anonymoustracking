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

    CRM_Anonymoustracking_Mailing_Page_Open::run();
  } elseif ($objectName === 'Mailing' && $op === 'edit') {

    //Civi::log()->debug('anonymoustracking_civicrm_pre ' . Civi::settings()->get('anonymous_tracking_default') . ' params: '. print_r($params, true));
    if ($anonymous_tracking_default = Civi::settings()->get('anonymous_tracking_default')) {
      $customFieldId = CRM_Anonymoustracking_Utils_Mailings::getMailingCustomFieldId();
      if (!isset($params['custom_' . $customFieldId])) {
        $customParams = [
          'entityID' => $id,
          'custom_' . $customFieldId => $anonymous_tracking_default,
        ];
        \CRM_Core_BAO_CustomValueTable::setValues($customParams);
      }
    }
  }
}

/**
 * Implements hook_civicrm_alterAngular().
 */
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

/**
 * Implements hook_civicrm_pageRun().
 */
function anonymoustracking_civicrm_pageRun(&$page)
{
  $pageName = get_class($page);

  if ($pageName == 'CRM_Mailing_Page_Report') {
    $smarty = CRM_Core_Smarty::singleton();
    $report = $smarty->get_template_vars('report');

    $mailing_id = $report['mailing']['id'];

    $anonymous_tracking = CRM_Anonymoustracking_Utils_Mailings::getAnonyousTrackingFromMailingId($mailing_id);

    // FIXME this is a trick to add a setting in the Content section of the Report
    // because in the Report template there is no way to add a setting
    $report['component'][] = [
      'type' => E::ts('Anonomous Tracking'),
      'name' => $anonymous_tracking ? ts('Enabled') : ts('Disabled'),
      'link' => '#',
    ];

    if ($anonymous_tracking) {
      CRM_Anonymoustracking_Utils_Reports::overrideReportWithAnonymousTracking($report, $mailing_id);
    }

    $smarty->assign('report', $report);
  }
}
