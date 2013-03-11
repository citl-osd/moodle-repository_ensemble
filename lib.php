<?php

// Repository to manage publish interface with Ensemble server
// Written by Liam Moran, March of 2012 <moran@illinois.edu>
// Updated by Liam Moran, Dec. of 2012
// This is for version 2.3--non-operational with older versions of Moodle
//
//    Copyright (C) 2012 Liam Moran
//    Copyright (C) 2013 Symphony Video, Inc.
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
//

class repository_ensemble extends repository {

  public static function get_instance_option_names() {
    return array('ensembleURL', 'serviceUser', 'servicePass', 'authDomain', 'evtype');
  }

  public static function instance_config_form($mform) {
    $required = get_string('required');
    $mform->addElement('text', 'ensembleURL', get_string('ensembleURL', 'repository_ensemble'), array('size' => '40'));
    $mform->addRule('ensembleURL', $required, 'required', null, 'client');
    $mform->addElement('static', null, '', get_string('ensembleURLHelp', 'repository_ensemble'));
    $mform->addElement('text', 'serviceUser', get_string('serviceUser', 'repository_ensemble'), array('size' => '40'));
    $mform->addRule('serviceUser', $required, 'required', null, 'client');
    $mform->addElement('static', null, '', get_string('serviceUserHelp', 'repository_ensemble'));
    $mform->addElement('passwordunmask', 'servicePass', get_string('servicePass', 'repository_ensemble'), array('size' => '40'));
    $mform->addRule('servicePass', $required, 'required', null, 'client');
    $mform->addElement('static', null, '', get_string('servicePassHelp', 'repository_ensemble'));
    $mform->addElement('text', 'authDomain', get_string('authDomain', 'repository_ensemble'), array('size' => '40'));
    $mform->addElement('static', null, '', get_string('authDomainHelp', 'repository_ensemble'));
    $mform->addElement('select', 'evtype', get_string('type', 'repository_ensemble'), array('video' => get_string('video', 'repository_ensemble'), 'playlist' => get_string('playlist', 'repository_ensemble')));
    $mform->addElement('static', null, '', get_string('typeHelp', 'repository_ensemble'));
  }

  public static function plugin_init() {
    $videoRepoId = repository::static_function('ensemble', 'create', 'ensemble', 0, get_system_context(), array('name' => get_string('videoRepo', 'repository_ensemble'), 'evtype' => 'video'), 0);
    $playlistRepoId = repository::static_function('ensemble', 'create', 'ensemble', 0, get_system_context(), array('name' => get_string('playlistRepo', 'repository_ensemble'), 'evtype' => 'playlist'), 0);
    return !empty($videoRepoId) && !empty($playlistRepoId);
  }

  public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
    parent::__construct($repositoryid, $context, $options);
  }

  public function get_listing($path='', $page='0') {
    global $CFG;
    $list = array();
    $list['object'] = array();
    $list['object']['type'] = 'text/html';
    $list['object']['src'] = $CFG->wwwroot . '/repository/ensemble/ext_chooser/index.php?repo_id=' . $this->id . '&ctx_id=' . $this->context->id;
    $list['nologin']  = true;
    $list['nosearch'] = true;
    $list['norefresh'] = true;
    return $list;
  }

  public function supported_filetypes() {
    return '*';
  }

  public function supported_returntypes() {
    return FILE_EXTERNAL;
  }

}
