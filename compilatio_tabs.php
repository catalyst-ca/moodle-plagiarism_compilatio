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
 * compilatio_tabs.php - Contains Plagiarism plugin script who create tab object in the plugin configuration web page.
 *
 * @package    plagiarism_cmp
 * @author     Compilatio <support@compilatio.net>
 * @copyright  2023 Compilatio.net {@link https://www.compilatio.net}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

// Get tabs' title.
$strplagiarism         = get_string('cmp', 'plagiarism_cmp');
$strplagiarismdefaults = get_string('compilatiodefaults', 'plagiarism_cmp');
$strautodiagnosis      = get_string('auto_diagnosis_title', 'plagiarism_cmp');
$strstatistics         = get_string('tabs_title_stats', 'plagiarism_cmp');
$strhelp               = get_string('tabs_title_help', 'plagiarism_cmp');

// Display Compilatio logo.
echo "<img id='cmp-logo' style='margin-bottom: 15px;' src='" . new moodle_url("/plagiarism/cmp/pix/compilatio.png") . "'>";
echo '<div style="clear:both"></div>';

// Create tabs.
$tabs = [];
$tabs[] = new tabobject('compilatiosettings', 'settings.php', $strplagiarism, $strplagiarism, false);
$tabs[] = new tabobject('compilatiodefaults', 'compilatio_defaults.php', $strplagiarismdefaults, $strplagiarismdefaults, false);
$tabs[] = new tabobject('compilatioautodiagnosis', 'autodiagnosis.php', $strautodiagnosis, $strautodiagnosis, false);
$tabs[] = new tabobject('compilatiostatistics', 'statistics.php', $strstatistics, $strstatistics, false);
$tabs[] = new tabobject('compilatiohelp', 'help.php', $strhelp, $strhelp, false);

// Display tabs.
print_tabs([$tabs], $currenttab);
