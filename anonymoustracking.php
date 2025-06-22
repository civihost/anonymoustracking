<?php

require_once 'anonymoustracking.civix.php';

use CRM_Anonymoustracking_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function anonymoustracking_civicrm_config(&$config): void {
  _anonymoustracking_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function anonymoustracking_civicrm_install(): void {
  _anonymoustracking_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function anonymoustracking_civicrm_enable(): void {
  _anonymoustracking_civix_civicrm_enable();
}
