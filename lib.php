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

  // Declare vars here
  private $ensembleURL;
  private $destinationID;
  private $defaultID;
  private $defaultName;

  /*****************
   * This block manages all of the administrative pages and configuration
   * *****************/

  public static function get_instance_option_names() {
    // Lists what needs to be provided by admin for each course instance
    return array('name','destinationID');
  }

  public static function instance_config_form($mform) {
    // Prints out the form for admin to give stuff from
    // get_instance_option_names return val
    $strrequired = get_string('required');
    $mform->addElement('text','destinationID',get_string('destinationID', 'repository_ensemble'), array('size'=>'40'));
    $mform->addRule('destinationID',$strrequired,'required',null,'client');
    return true;
  }

  public static function instance_form_validation($mform, $data, $errors) {
    // For validating the admin's input to the config form
    // For now, just checking that it's non-empty
    // But it should maybe make an API call down the road if we
    // ensure that every destination is pre-loaded with some video
    // checking for a non-empty response or something else what's sane
    if (empty($data['destinationID'])) {
      $errors['destinationID'] = get_string('invalidDestinationID','repository_ensemble');
      }
    return $errors;
  }

  public static function get_type_option_names() {
    // This is where we set global repository variables, shared
    // by all course instances
    // Here it's a URL to the ensemble server's simpleAPI interface
    // and a default destinationID (probably to a set of moodle tutorial videos)
    return array('ensembleURL','defaultName','defaultID');
  }

  public static function type_config_form($mform, $classname = 'repository') {
    // Prints out a form to collect type_options from admin
    $ensembleURL = get_config('ensembleURL');
    if (empty($ensembleURL)){
      $ensembleURL = '';
    }
    $defaultID = get_config('defaultID');
    if (empty($defaultID)){
      $defaultID = '';
    }
    $defaultName = get_config('defaultName');
    if (empty($defaultName)) {
      $defaultName = '';
    }

    $strrequired = get_string('required');
    $mform->addElement('static',null,'',get_string('ensembleURLHelp','repository_ensemble'));
    $mform->addElement('text','ensembleURL',get_string('ensembleURL', 'repository_ensemble'), array('value'=>$ensembleURL,'size' => '40'));
    $mform->addRule('ensembleURL',$strrequired,'required',null,'client');
    $mform->addElement('text','defaultName',get_string('defaultName','repository_ensemble'), array('value'=>$defaultName,'size' => '40'));
    $mform->addRule('defaultName', $strrequired, 'required', null, 'client');
    $mform->addElement('static',null,'',get_string('defaultIDHelp','repository_ensemble'));
    $mform->addElement('text','defaultID', get_string('defaultID','repository_ensemble'), array('value'=>$defaultID,'size'=>'40'));
    $mform->addRule('defaultID', $strrequired, 'required', null, 'client');
  }

  public static function type_form_validation($mform, $data, $errors) {
    // A little bit of trivial baby-sitting
    if (empty($data['ensembleURL'])) {
      $errors['ensembleURL'] = 'I really need to know where it is';
    } elseif (empty($data['defaultID'])) {
      $errors['defaultID'] = 'A default destination is required!';
    } elseif (empty($data['defaultName'])) {
      $errors['defaultName'] = 'You should name the default repo';
    }
    return $errors;
  }

  public static function plugin_init() {
    // Creates a default instance when the repo plugin is created by admin
    // This default will show up for all courses, so make it a good one
    $id = repository::static_function('ensemble','create','ensemble',0,get_system_context(),array('name' => get_config('ensemble','defaultName'),'destinationID' => get_config('ensemble','defaultID')),1);
    if (empty($id)) {
       return false;
    } else {
      return true;
    }
  }

  /***************
   * Now the repository code, interfacing moodle with ensemble as configured in
   * the type configuration
   */

  public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
    // Constructor needs to grab the parameters set by admin
    $this->ensembleURL = get_config('ensemble','ensembleURL');
    parent::__construct($repositoryid, $context,$options);
  }

  public function get_listing($path='', $page='0') {
    global $CFG;
    $list = array();
    $list['object'] = array();
    $list['object']['type'] = 'text/html';
    $list['object']['src'] = $CFG->wwwroot . '/repository/ensemble/ext_chooser/video.php';
    $list['nologin']  = true;
    $list['nosearch'] = true;
    $list['norefresh'] = true;
    return $list;
  }

  public function supported_filetypes() {
    // see filelib.php &get_mimetypes_array()
    // allows links with .mp4 at the end.
    return array('video');
  }

  public function supported_returntypes() {
    // We're returning external file references, not pointers to content on moodle
    return FILE_EXTERNAL;
  }

// End of class
}
