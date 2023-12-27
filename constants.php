<?php
defined( 'ABSPATH' ) || exit;

/** Path and URL constants **/
const ADMTH = 'Dashboard Admin Theme'; // WRCS: DEFINED_VERSION.
const ADMTH_VERSION   = '1.0.0'; // WRCS: DEFINED_VERSION.

define( 'ADMTH_URL', plugins_url( '', __FILE__ ) );

define( 'ADMTH_DIR', plugin_dir_path( __FILE__ ) );

const ADMTH_INC_DIR = ADMTH_DIR . 'includes';
const ADMTH_INC_URL = ADMTH_URL . '/includes';

const ADMTH_ASSETS_DIR = ADMTH_DIR . 'assets';
const ADMTH_ASSETS_URL = ADMTH_URL . '/assets';

const ADMTH_SDK_DIR = ADMTH_DIR . 'sdk';
const ADMTH_SDK_URL = ADMTH_URL . '/sdk';

const ADMTH_SRC_DIR = ADMTH_DIR . 'src';
const ADMTH_SRC_URL = ADMTH_URL . '/src';

require_once ADMTH_SRC_DIR . '/init.php';
