<?php

/*
	Plugin Name: Lottie Animations
	Description: Include Lotties with shortcodes
	Author: Absatzformat GmbH
	Version: 1.0.1
	Author URI: https://absatzformat.de
*/

use Absatzformat\Wordpress\LottieAnimations\LottieAnimations;

defined('WPINC') || die();

require __DIR__ . '/src/LottieAnimations.php';

LottieAnimations::getInstance();
