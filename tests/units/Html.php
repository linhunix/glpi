<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2017 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
*/

namespace tests\units;

use \atoum;

/* Test for inc/html.class.php */

class Html extends atoum {

   public function testConvDate() {
      $this->variable(\Html::convDate(null))->isNull();
      $this->variable(\Html::convDate('NULL'))->isNull();
      $this->variable(\Html::convDate(''))->isNull();

      $mydate = date('Y-m-d H:i:s');

      $expected = date('Y-m-d');
      unset($_SESSION['glpidate_format']);
      $this->string(\Html::convDate($mydate))->isIdenticalTo($expected);
      $_SESSION['glpidate_format'] = 0;
      $this->string(\Html::convDate($mydate))->isIdenticalTo($expected);

      $this->string(\Html::convDate(date('Y-m-d')))->isIdenticalTo($expected);

      $expected = date('d-m-Y');
      $this->string(\Html::convDate($mydate, 1))->isIdenticalTo($expected);

      $expected = date('m-d-Y');
      $this->string(\Html::convDate($mydate, 2))->isIdenticalTo($expected);
   }

   public function testConvDateTime() {
      $this->variable(\Html::convDateTime(null))->isNull();
      $this->variable(\Html::convDateTime('NULL'))->isNull;

      $mydate = date('Y-m-d H:i:s');

      $expected = date('Y-m-d H:i');
      $this->string(\Html::convDateTime($mydate))->isIdenticalTo($expected);

      $expected = date('d-m-Y H:i');
      $this->string(\Html::convDateTime($mydate, 1))->isIdenticalTo($expected);

      $expected = date('m-d-Y H:i');
      $this->string(\Html::convDateTime($mydate, 2))->isIdenticalTo($expected);
   }

   public function testCleanInputText() {
      $origin = 'This is a \'string\' with some "replacements" needed, but not « others »!';
      $expected = 'This is a &apos;string&apos; with some &quot;replacements&quot; needed, but not « others »!';
      $this->string(\Html::cleanInputText($origin))->isIdenticalTo($expected);
   }

   public function cleanParametersURL() {
      $url = 'http://host/glpi/path/to/file.php?var1=2&var2=3';
      $expected = 'http://host/glpi/path/to/file.php';
      $this->string(\Html::cleanParametersURL($url))->isIdenticalTo($expected);
   }

   public function testNl2br_deep() {
      $origin = "A string\nwith breakline.";
      $expected = "A string<br />\nwith breakline.";
      $this->string(\Html::nl2br_deep($origin))->isIdenticalTo($expected);

      $origin = [
         "Another string\nwith breakline.",
         "And another\none"
      ];
      $expected = [
         "Another string<br />\nwith breakline.",
         "And another<br />\none"
      ];
      $this->array(\Html::nl2br_deep($origin))->isIdenticalTo($expected);
   }

   public function testResume_text() {
      $origin = 'This is a very long string which will be truncated by a dedicated method. ' .
         'If the string is not truncated, well... We\'re wrong and got a very serious issue in our codebase!' .
         'And if the string has been correctly truncated, well... All is ok then, let\'s show if all the other tests are OK :)';
      $expected = 'This is a very long string which will be truncated by a dedicated method. ' .
         'If the string is not truncated, well... We\'re wrong and got a very serious issue in our codebase!' .
         'And if the string has been correctly truncated, well... All is ok then, let\'s show i&nbsp;(...)';
      $this->string(\Html::resume_text($origin))->isIdenticalTo($expected);

      $origin = 'A string that is longer than 10 characters.';
      $expected = 'A string t&nbsp;(...)';
      $this->string(\Html::resume_text($origin, 10))->isIdenticalTo($expected);
   }

   public function testResume_name() {
      $origin = 'This is a very long string which will be truncated by a dedicated method. ' .
         'If the string is not truncated, well... We\'re wrong and got a very serious issue in our codebase!' .
         'And if the string has been correctly truncated, well... All is ok then, let\'s show if all the other tests are OK :)';
      $expected = 'This is a very long string which will be truncated by a dedicated method. ' .
         'If the string is not truncated, well... We\'re wrong and got a very serious issue in our codebase!' .
         'And if the string has been correctly truncated, well... All is ok then, let\'s show i...';
      $this->string(\Html::resume_name($origin))->isIdenticalTo($expected);

      $origin = 'A string that is longer than 10 characters.';
      $expected = 'A string t...';
      $this->string(\Html::resume_name($origin, 10))->isIdenticalTo($expected);
   }

   public function testCleanPostForTextArea() {
      $origin = "A text that \\\"would\\\" be entered in a \\'textarea\\'\\nWith breakline\\r\\nand breaklines.";
      $expected = "A text that \"would\" be entered in a 'textarea'\nWith breakline\nand breaklines.";
      $this->string(\Html::cleanPostForTextArea($origin))->isIdenticalTo($expected);

      $aorigin = [
        $origin,
        "Another\\none!"
      ];
      $aexpected = [
         $expected,
         "Another\none!"
      ];
      $this->array(\Html::cleanPostForTextArea($aorigin))->isIdenticalTo($aexpected);
   }

   public function providerClean() {
      return [
            ['<p>Hello<script type="text/javascript">alert("Damn!");</script></p>', 'Hello', '<p>Hello</p>'],
      ];
   }

   /**
    * @dataProvider providerClean
    */
   public function testCleanDropTags($in, $outnotag, $outtag) {
      $this->string(\Html::clean($in, true))->isIdenticalTo($outnotag);
   }

   /**
    * @dataProvider providerClean
    */
   public function testCleanKeepTags($in, $outnotag, $outtag) {
      $this->string(\Html::clean($in, false))->isIdenticalTo($outtag);
   }

   public function testFormatNumber() {
      $_SESSION['glpinumber_format'] = 0;
      $origin = '';
      $expected = '0.00';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $origin = '1207.3';

      $expected = '1&nbsp;207.30';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $expected = '1207.30';
      $this->string(\Html::formatNumber($origin, true))->isIdenticalTo($expected);

      $origin = 124556.693;
      $expected = '124&nbsp;556.69';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $origin = 120.123456789;

      $expected = '120.12';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $expected = '120.12346';
      $this->string(\Html::formatNumber($origin, false, 5))->isIdenticalTo($expected);

      $expected = '120';
      $this->string(\Html::formatNumber($origin, false, 0))->isIdenticalTo($expected);

      $origin = 120.999;
      $expected = '121.00';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);
      $expected = '121';
      $this->string(\Html::formatNumber($origin, false, 0))->isIdenticalTo($expected);

      $this->string(\Html::formatNumber('-'))->isIdenticalTo('-');

      $_SESSION['glpinumber_format'] = 2;

      $origin = '1207.3';
      $expected = '1&nbsp;207,30';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $_SESSION['glpinumber_format'] = 3;

      $origin = '1207.3';
      $expected = '1207.30';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $_SESSION['glpinumber_format'] = 4;

      $origin = '1207.3';
      $expected = '1207,30';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);

      $_SESSION['glpinumber_format'] = 1337;
      $origin = '1207.3';

      $expected = '1,207.30';
      $this->string(\Html::formatNumber($origin))->isIdenticalTo($expected);
   }

   public function testTimestampToString() {
      $expected = '0 seconds';
      $this->string(\Html::timestampToString(null))->isIdenticalTo($expected);
      $this->string(\Html::timestampToString(''))->isIdenticalTo($expected);
      $this->string(\Html::timestampToString(0))->isIdenticalTo($expected);

      $tstamp = 57226;
      $expected = '15 hours 53 minutes 46 seconds';
      $this->string(\Html::timestampToString($tstamp))->isIdenticalTo($expected);

      $tstamp = -57226;
      $expected = '- 15 hours 53 minutes 46 seconds';
      $this->string(\Html::timestampToString($tstamp))->isIdenticalTo($expected);

      $tstamp = 1337;
      $expected = '22 minutes 17 seconds';
      $this->string(\Html::timestampToString($tstamp))->isIdenticalTo($expected);

      $expected = '22 minutes';
      $this->string(\Html::timestampToString($tstamp, false))->isIdenticalTo($expected);

      $tstamp = 54;
      $expected = '54 seconds';
      $this->string(\Html::timestampToString($tstamp))->isIdenticalTo($expected);
      $this->string(\Html::timestampToString($tstamp, false))->isIdenticalTo($expected);

      $tstamp = 157226;
      $expected = '1 days 19 hours 40 minutes 26 seconds';
      $this->string(\Html::timestampToString($tstamp))->isIdenticalTo($expected);

      $expected = '1 days 19 hours 40 minutes';
      $this->string(\Html::timestampToString($tstamp, false))->isIdenticalTo($expected);

      $expected = '43 hours 40 minutes 26 seconds';
      $this->string(\Html::timestampToString($tstamp, true, false))->isIdenticalTo($expected);

      $expected = '43 hours 40 minutes';
      $this->string(\Html::timestampToString($tstamp, false, false))->isIdenticalTo($expected);
   }

   public function testWeblink_extract() {
      $origin = '<a href="http://glpi-project.org" class="example">THE GLPI Project!</a>';
      $expected = 'http://glpi-project.org';
      $this->string($expected, \Html::weblink_extract($origin))->isIdenticalTo($expected);

      $origin = '<a href="http://glpi-project.org/?one=two">THE GLPI Project!</a>';
      $expected = 'http://glpi-project.org/?one=two';
      $this->string(\Html::weblink_extract($origin))->isIdenticalTo($expected);

      //These ones does not work, but probably should...
      $origin = '<a class="example" href="http://glpi-project.org">THE GLPI Project!</a>';
      $expected = $origin;
      $this->string(\Html::weblink_extract($origin))->isIdenticalTo($expected);

      $origin = '<a href="http://glpi-project.org" class="example">THE <span>GLPI</span> Project!</a>';
      $expected = $origin;
      $this->string(\Html::weblink_extract($origin))->isIdenticalTo($expected);
   }

   public function testGetMenuInfos() {
      $menu = \Html::getMenuInfos();
      $this->integer(count($menu))->isIdenticalTo(8);

      $expected = [
         'assets',
         'helpdesk',
         'management',
         'tools',
         'plugins',
         'admin',
         'config',
         'preference'
      ];
      $this->array($menu)
         ->hasSize(count($expected))
         ->hasKeys($expected);

      $expected = [
         'Computer',
         'Monitor',
         'Software',
         'NetworkEquipment',
         'Peripheral',
         'Printer',
         'CartridgeItem',
         'ConsumableItem',
         'Phone'
      ];
      $this->string($menu['assets']['title'])->isIdenticalTo('Assets');
      $this->array($menu['assets']['types'])->isIdenticalTo($expected);

      $expected = [
         'Ticket',
         'Problem',
         'Change',
         'Planning',
         'Stat',
         'TicketRecurrent'
      ];
      $this->string($menu['helpdesk']['title'])->isIdenticalTo('Assistance');
      $this->array($menu['helpdesk']['types'])->isIdenticalTo($expected);

      $expected = [
         'SoftwareLicense',
         'Budget',
         'Supplier',
         'Contact',
         'Contract',
         'Document'
      ];
      $this->string($menu['management']['title'])->isIdenticalTo('Management');
      $this->array($menu['management']['types'])->isIdenticalTo($expected);

      $expected = [
         'Project',
         'Reminder',
         'RSSFeed',
         'KnowbaseItem',
         'ReservationItem',
         'Report',
         'MigrationCleaner',
         'SavedSearch'
      ];
      $this->string($menu['tools']['title'])->isIdenticalTo('Tools');
      $this->array($menu['tools']['types'])->isIdenticalTo($expected);

      $expected = [];
      $this->string($menu['plugins']['title'])->isIdenticalTo('Plugins');
      $this->array($menu['plugins']['types'])->isIdenticalTo($expected);

      $expected = [
         'User',
         'Group',
         'Entity',
         'Rule',
         'Profile',
         'QueuedMail',
         'Backup',
         'Event'
      ];
      $this->string($menu['admin']['title'])->isIdenticalTo('Administration');
      $this->array($menu['admin']['types'])->isIdenticalTo($expected);

      $expected = [
         'CommonDropdown',
         'CommonDevice',
         'Notification',
         'SLA',
         'Config',
         'Control',
         'Crontask',
         'Auth',
         'MailCollector',
         'Link',
         'Plugin'
      ];
      $this->string($menu['config']['title'])->isIdenticalTo('Setup');
      $this->array($menu['config']['types'])->isIdenticalTo($expected);

      $this->string($menu['preference']['title'])->isIdenticalTo('My settings');
      $this->array($menu['preference'])->notHasKey('types');
      $this->string($menu['preference']['default'])->isIdenticalTo('/front/preference.php');
   }

   public function testGetCopyrightMessage() {
      $message = \Html::getCopyrightMessage();
      $this->string($message)
         ->contains(GLPI_VERSION)
         ->contains(GLPI_YEAR);
   }

   public function testCss() {
      global $CFG_GLPI;

      //fake files
      $fake_files = [
         'file.css',
         'file.min.css',
         'other.css',
         'other-min.css'
      ];
      $dir = str_replace(GLPI_ROOT, '', GLPI_TMP_DIR);
      $base_expected = '<link rel="stylesheet" type="text/css" href="'.
         $CFG_GLPI['root_doc'] . $dir .'/%url?v='. GLPI_VERSION .'" %attrs>';
      $base_attrs = 'media="screen"';

      //create test files
      foreach ($fake_files as $fake_file) {
         touch(GLPI_TMP_DIR . '/' . $fake_file);
      }

      //expect minified file
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.min.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css'))->isIdenticalTo($expected);

      //explicitely require not minified file
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css', [], false))->isIdenticalTo($expected);

      //activate debug mode: expect not minified file
      $_SESSION['glpi_use_mode'] = \Session::DEBUG_MODE;
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css'))->isIdenticalTo($expected);
      $_SESSION['glpi_use_mode'] = \Session::NORMAL_MODE;

      //expect original file
      $expected = str_replace(
         ['%url', '%attrs'],
         ['nofile.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/nofile.css'))->isIdenticalTo($expected);

      //expect original file
      $expected = str_replace(
         ['%url', '%attrs'],
         ['other.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/other.css'))->isIdenticalTo($expected);

      //expect original file
      $expected = str_replace(
         ['%url', '%attrs'],
         ['other-min.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/other-min.css'))->isIdenticalTo($expected);

      //expect minified file, print media
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.min.css', 'media="print"'],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css', ['media' => 'print']))->isIdenticalTo($expected);

      //expect minified file, screen media
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.min.css', $base_attrs],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css', ['media' => '']))->isIdenticalTo($expected);

      //expect minified file and specific version
      $fake_version = '0.0.1';
      $expected = str_replace(
         ['%url', '%attrs', GLPI_VERSION],
         ['file.min.css', $base_attrs, $fake_version],
         $base_expected
      );
      $this->string(\Html::css($dir . '/file.css', ['version' => $fake_version]))->isIdenticalTo($expected);

      //expect minified file with added attributes
      $expected = str_replace(
         ['%url', '%attrs'],
         ['file.min.css', 'attribute="one" ' . $base_attrs],
         $base_expected
      );
      $this->string($expected, \Html::css($dir . '/file.css', ['attribute' => 'one']))->isIdenticalTo($expected);

      //remove test files
      foreach ($fake_files as $fake_file) {
         unlink(GLPI_TMP_DIR . '/' . $fake_file);
      }
   }

   public function testScript() {
      global $CFG_GLPI;

      //fake files
      $fake_files = [
         'file.js',
         'file.min.js',
         'other.js',
         'other-min.js'
      ];
      $dir = str_replace(GLPI_ROOT, '', GLPI_TMP_DIR);
      $base_expected = '<script type="text/javascript" src="'.
         $CFG_GLPI['root_doc'] . $dir .'/%url?v='. GLPI_VERSION .'"></script>';

      //create test files
      foreach ($fake_files as $fake_file) {
         touch(GLPI_TMP_DIR . '/' . $fake_file);
      }

      //expect minified file
      $expected = str_replace(
         '%url',
         'file.min.js',
         $base_expected
      );
      $this->string(\Html::script($dir . '/file.js'))->isIdenticalTo($expected);

      //explicitely require not minified file
      $expected = str_replace(
         '%url',
         'file.js',
         $base_expected
      );
      $this->string(\Html::script($dir . '/file.js', [], false))->isIdenticalTo($expected);

      //activate debug mode: expect not minified file
      $_SESSION['glpi_use_mode'] = \Session::DEBUG_MODE;
      $expected = str_replace(
         '%url',
         'file.js',
         $base_expected
      );
      $this->string($expected, \Html::script($dir . '/file.js'))->isIdenticalTo($expected);
      $_SESSION['glpi_use_mode'] = \Session::NORMAL_MODE;

      //expect original file
      $expected = str_replace(
         '%url',
         'nofile.js',
         $base_expected
      );
      $this->string(\Html::script($dir . '/nofile.js'))->isIdenticalTo($expected);

      //expect original file
      $expected = str_replace(
         '%url',
         'other.js',
         $base_expected
      );
      $this->string(\Html::script($dir . '/other.js'))->isIdenticalTo($expected);

      //expect original file
      $expected = str_replace(
         '%url',
         'other-min.js',
         $base_expected
      );
      $this->string(\Html::script($dir . '/other-min.js'))->isIdenticalTo($expected);

      //expect minified file and specific version
      $fake_version = '0.0.1';
      $expected = str_replace(
         ['%url', GLPI_VERSION],
         ['file.min.js', $fake_version],
         $base_expected
      );
      $this->string(\Html::script($dir . '/file.js', ['version' => $fake_version]))->isIdenticalTo($expected);

      //remove test files
      foreach ($fake_files as $fake_file) {
         unlink(GLPI_TMP_DIR . '/' . $fake_file);
      }
   }

   public function testManageRefreshPage() {
      //no session refresh, no args => no timer
      if (isset($_SESSION['glpirefresh_ticket_list'])) {
         unset($_SESSION['glpirefresh_ticket_list']);
      }

      $base_script = \Html::scriptBlock("window.setInterval(function() {
               ##CALLBACK##
            }, ##TIMER##);");

      $expected = '';
      $message = \Html::manageRefreshPage();
      $this->string($message)->isIdenticalTo($expected);

      //Set session refresh to one minute
      $_SESSION['glpirefresh_ticket_list'] = 1;
      $expected = str_replace("##CALLBACK##", "window.location.reload()", $base_script);
      $expected = str_replace("##TIMER##", 1 * MINUTE_TIMESTAMP * 1000, $expected);
      $message = \Html::manageRefreshPage();
      $this->string($message)->isIdenticalTo($expected);

      $expected = str_replace("##CALLBACK##", '$(\'#mydiv\').remove();', $base_script);
      $expected = str_replace("##TIMER##", 1 * MINUTE_TIMESTAMP * 1000, $expected);
      $message = \Html::manageRefreshPage(false, '$(\'#mydiv\').remove();');
      $this->string($message)->isIdenticalTo($expected);

      $expected = str_replace("##CALLBACK##", "window.location.reload()", $base_script);
      $expected = str_replace("##TIMER##", 3 * MINUTE_TIMESTAMP * 1000, $expected);
      $message = \Html::manageRefreshPage(3);
      $this->string($message)->isIdenticalTo($expected);

      $expected = str_replace("##CALLBACK##", '$(\'#mydiv\').remove();', $base_script);
      $expected = str_replace("##TIMER##", 3 * MINUTE_TIMESTAMP * 1000, $expected);
      $message = \Html::manageRefreshPage(3, '$(\'#mydiv\').remove();');
      $this->string($message)->isIdenticalTo($expected);
   }

   public function testGenerateMenuSession() {
      //login to get session
      $auth = new \Auth();
      $this->boolean($auth->login(TU_USER, TU_PASS, true))->isTrue();

      $menu = \Html::generateMenuSession(true);

      $this->array($_SESSION)
         ->hasKey('glpimenu');

      $this->array($menu)
            ->isIdenticalTo($_SESSION['glpimenu'])
            ->hasKey('assets')
            ->hasKey('helpdesk')
            ->hasKey('management')
            ->hasKey('tools')
            ->hasKey('plugins')
            ->hasKey('admin')
            ->hasKey('config')
            ->hasKey('preference');

      foreach ($menu as $menu_entry) {
         $this->array($menu_entry)
            ->hasKey('title');

         if (isset($menu_entry['content'])) {

            $this->array($menu_entry)
               ->hasKey('types');

            foreach ($menu_entry['content'] as $submenu_label => $submenu) {
               if ($submenu_label === 'is_multi_entries') {
                  continue;
               }

               $this->array($submenu)
                  ->hasKey('title')
                  ->hasKey('page');
            }
         }
      }
   }

   public function testFuzzySearch() {
      //login to get session
      $auth = new \Auth();
      $this->boolean($auth->login(TU_USER, TU_PASS, true))->isTrue();

      // init menu
      \Html::generateMenuSession(true);

      // test modal
      $modal = \Html::FuzzySearch('getHtml');
      $this->string($modal)
         ->contains("id='fuzzysearch'")
         ->contains("class='results'");

      // test retrieving entries
      $default = json_decode(\Html::FuzzySearch(), true);
      $entries = json_decode(\Html::FuzzySearch('getList'), true);
      $this->array($default)
         ->isNotEmpty()
         ->isIdenticalTo($entries)
         ->hasKey(0)
         ->size->isGreaterThan(5);

      foreach ($default as $entry) {
         $this->array($entry)
            ->hasKey('title')
            ->hasKey('url');
      }
   }
}
