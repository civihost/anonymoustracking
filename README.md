# Anonymous Tracking

This CiviCRM extension provides a mechanism to anonymize the tracking of email opens and clicks within the CiviMail component. It enables the collection of important campaign statistics, such as open and click-through rates, without compromising individual user privacy by decoupling tracking events from specific contacts.

The core functionality involves intercepting the standard CiviMail tracking URLs. When anonymous tracking is enabled for a given mailing, the extension redirects the tracking data to dedicated anonymous storage tables, thereby severing any link to the originating contact record.

This extension is licensed under [AGPL-3.0](LICENSE.txt).

## How it works

The extension introduces two new tables to the CiviCRM schema to handle anonymous data:

* `civicrm_anonymoustracking_mailing_opened`: Stores anonymous open events, recording the `anonymous_id`, `mailing_id` and a `time_stamp`.
* `civicrm_anonymoustracking_mailing_url_open`: Stores anonymous click-through events, recording the  `anonymous_id`, `mailing_id`, `trackable_url_id`, a `time_stamp`, and a unique anonymous identifier.

To calculate unique clicks without user identification, the extension generates a non-reversible **anonymous identifier for each recipient**. This is achieved by applying the `hash_hmac` cryptographic function with a `sha256` algorithm to the event queue ID (`queue_id`), using the site-specific `CIVICRM_SITE_KEY` as the secret key. This process ensures that multiple clicks from the same recipient are counted as a single unique event, while making it computationally impossible to reverse the hash and identify the recipient.

## Requirements

* PHP v7.4+
* CiviCRM v6.0+

## Installation

Install as a regular CiviCRM extension.

## Usage

1. **Global configuration**: A global setting "_Enable anonymous tracking by default_" is provided under `Administer > CiviMail > CiviMail Component Settings` to enable anonymous tracking by default for all new mailings.

![civimail_component_settings](https://github.com/user-attachments/assets/2fc6c422-b597-4a56-a357-cb9fafcc1494)

2. **Per-mailing configuration**: When composing a new mailing, go to the **Tracking** tab. A new "Anonymous tracking" checkbox will be present, allowing you to control the tracking method for that specific campaign. If it was set up previously to be enabled by default the checkbox will already be checked.

![image](https://github.com/user-attachments/assets/39568c73-e967-4464-a640-f667160291cb)

3. **View reports**: Access the Mailing Report as usual. If anonymous tracking was active, the aggregate statistics will be displayed. The drill-down lists for opens and clicks will be empty to ensure privacy.

## Known issues
* **Missing click-through report**: A detailed report for anonymous click-throughs, showing which specific links were clicked, is not yet implemented. Currently, only the aggregate click statistics are available in the main mailing report.
* For any other issues, please check GitHub issue tracker: [https://github.com/civihost/anonymoustracking/issues](https://github.com/civihost/anonymoustracking/issues).

## Support

Please post bug reports in the issue tracker of this project on GitHub: [https://github.com/civihost/anonymoustracking/issues](https://github.com/civihost/anonymoustracking/issues).

While we do our best to provide free community support for this extension, please consider financially contributing to support or development of this extension.

This is mantained by Samuele Masetto from [CiviHOST](https://www.civihost.it/) who you can contact for help, support and further development.
