<?php

/*
Plugin Name: BWR Toolkit
Plugin URI: https://digital.bigwestrotaract.org
Description: Big West Rotaract utilities; please leave enabled
Version: 1.0
Author: Benjamin Sihota
Author URI: https://www.bensihota.com
License: GPLv3
*/

/**
 * Multisite primary (district) site features
 */
if ( is_main_site() ) {
    include_once 'bwrt-page-template.php';
}