<?php

// This repository provides an interface to an Ensemble server
// and presents the media assigned to a course via an Ensemble directory
// as well as a search interface to add other media available in a system-wide
// public catalog.
//
// It was written by Liam Moran <moran@illinois.edu> in March, 2012
//
// GPL language goes here.

defined('MOODLE_INTERNAL') || die();

// You are on the MOODLE_25_STABLE branch.  Do NOT update the version
// branching date rather update the release increment.
$plugin->version        = 2013082800;
$plugin->release        = '2.5 (Build: 2013082800)';
$plugin->maturity       = MATURITY_STABLE;
$plugin->requires       = 2013051400;
$plugin->component      = 'repository_ensemble';
$plugin->dependencies   = array('filter_ensemble' => 2013082800);
