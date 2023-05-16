<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism.php - allows the admin to configure plagiarism stuff
 *
 * @package    plagiarism_cmp
 * @author     Compilatio <support@compilatio.net>
 * @copyright  2023 Compilatio.net {@link https://www.compilatio.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/plagiarismlib.php');
require_once($CFG->dirroot . '/plagiarism/cmp/lib.php');
require_once($CFG->dirroot . '/plagiarism/cmp/compilatio_form.php');
require_once($CFG->dirroot . '/plagiarism/cmp/classes/compilatio/api.php');

require_login();
admin_externalpage_setup('plagiarismcompilatio');

$context = context_system::instance();
require_capability('moodle/site:config', $context, $USER->id, true, 'nopermissions');

$plagiarismplugin = new plagiarism_plugin_cmp();
$plagiarismsettings = (array) get_config('plagiarism_cmp');

// Test if compilatio is enabled.
if (isset($plagiarismsettings['enabled'])) {
    $enabledsuccess = $plagiarismsettings['enabled'] === '1';
} else {
    $enabledsuccess = false;
}

// Connection test.
$compilatio = new CompilatioAPI('test');
if ($compilatio->check_apikey() == 'Forbidden ! Your api key is invalid') {
    $connectionsuccess = true;
} else {
    $connectionsuccess = false;
}

// Test if Compilatio is enabled for assign.
if (isset($plagiarismsettings['enable_mod_assign'])) {
    $assignsuccess = $plagiarismsettings['enable_mod_assign'];
} else {
    $assignsuccess = false;
}

// Test if Compilatio is enabled for workshops.
if (isset($plagiarismsettings['enable_mod_workshop'])) {
    $workshopsuccess = $plagiarismsettings['enable_mod_workshop'];
} else {
    $workshopsuccess = false;
}

// Test if Compilatio is enabled for forums.
if (isset($plagiarismsettings['enable_mod_forum'])) {
    $forumsuccess = $plagiarismsettings['enable_mod_forum'];
} else {
    $forumsuccess = false;
}

// Test if Compilatio is enabled for quiz.
if (isset($plagiarismsettings['enable_mod_quiz'])) {
    $quizsuccess = $plagiarismsettings['enable_mod_quiz'];
} else {
    $quizsuccess = false;
}

// API key test.
$compilatio = new CompilatioAPI($plagiarismsettings['apikey']);
if ($compilatio->check_apikey()) {
    $apikeysuccess = true;
} else {
    $apikeysuccess = false;
}

echo $OUTPUT->header();
$currenttab = 'compilatioautodiagnosis';
require_once($CFG->dirroot . '/plagiarism/cmp/compilatio_tabs.php');
echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');

$alerts = [];

if ($enabledsuccess) {
    $alerts[] = ['success', get_string('plugin_enabled', 'plagiarism_cmp')];
} else {
    $alerts[] = ['danger', get_string('plugin_disabled', 'plagiarism_cmp')];
}

if ($connectionsuccess) {
    $alerts[] = ['success', get_string('webservice_ok', 'plagiarism_cmp')];
} else {
    $alerts[] = ['danger', get_string('webservice_not_ok', 'plagiarism_cmp')];
}

if ($apikeysuccess) {
    $alerts[] = ['success', get_string('api_key_valid', 'plagiarism_cmp')];
} else if (!$connectionsuccess) {
    $alerts[] = ['warning', get_string('api_key_not_tested', 'plagiarism_cmp')];
} else {
    $alerts[] = ['danger', get_string('api_key_not_valid', 'plagiarism_cmp')];
}

$lastcron = get_config('plagiarism_cmp', 'last_cron');

if ($lastcron == null) {
    // Cron function in lib.php has never been called.
    $alerts[] = ['danger', get_string('cron_check_never_called', 'plagiarism_cmp')];
} else {
    $cronfrequency = get_config('plagiarism_cmp', 'cron_frequency');

    if ($cronfrequency == null) {
        // We don't have data about frequency yet.
        if ($lastcron <= strtotime('-1 hour')) {
            // Cron hasn't been called within the previous hour.
            $alerts[] = ['warning', get_string('cron_check', 'plagiarism_cmp', userdate($lastcron)) . ' ' .
                get_string('cron_check_not_ok', 'plagiarism_cmp') . ' ' .
                get_string('cron_recommandation', 'plagiarism_cmp')
            ];
        } else {
            $alerts[] = ['success', get_string('cron_check', 'plagiarism_cmp', userdate($lastcron)) . ' ' .
                get_string('cron_recommandation', 'plagiarism_cmp')];
        }
    } else {
        if ($cronfrequency > 15 || $lastcron <= strtotime('-1 hour')) {// Warning.
            $alert = get_string('cron_check', 'plagiarism_cmp', userdate($lastcron)) . ' ';

            if ($lastcron <= strtotime('-1 hour')) {
                // Cron hasn't been called within the previous hour.
                $alert .= get_string('cron_check_not_ok', 'plagiarism_cmp') . ' ';
            }

            $alert .= get_string('cron_frequency', 'plagiarism_cmp', $cronfrequency) . ' ';


            $alerts[] = ['warning', $alert . get_string('cron_recommandation', 'plagiarism_cmp')];
        } else {
            // Cron is called more than once every 15 minutes.
            $alerts[] = ['success', get_string('cron_check', 'plagiarism_cmp', userdate($lastcron)) . ' ' .
                get_string('cron_frequency', 'plagiarism_cmp', $cronfrequency) . ' ' .
                get_string('cron_recommandation', 'plagiarism_cmp')
            ];
        }
    }
}

if ($assignsuccess) {
    $alerts[] = ['success', get_string('plugin_enabled_assign', 'plagiarism_cmp')];
} else {
    $alerts[] = ['warning', get_string('plugin_disabled_assign', 'plagiarism_cmp')];
}


if ($workshopsuccess) {
    $alerts[] = ['success', get_string('plugin_enabled_workshop', 'plagiarism_cmp')];
} else {
    $alerts[] = ['warning', get_string('plugin_disabled_workshop', 'plagiarism_cmp')];
}


if ($forumsuccess) {
    $alerts[] = ['success', get_string('plugin_enabled_forum', 'plagiarism_cmp')];
} else {
    $alerts[] = ['warning', get_string('plugin_disabled_forum', 'plagiarism_cmp')];
}

if ($quizsuccess) {
    $alerts[] = ['success', get_string('plugin_enabled_quiz', 'plagiarism_cmp')];
} else {
    $alerts[] = ['warning', get_string('plugin_enabled_quiz', 'plagiarism_cmp')];
}

/*
 * Display alerts :
 * Index 0 for criticality & index 1 for message
 */
foreach ($alerts as $alert) {
    echo "<div class='cmp-alert cmp-alert-" . $alert[0] . "'>" . $alert[1] . '</div>';
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
