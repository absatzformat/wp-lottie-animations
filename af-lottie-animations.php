<?php

/*
	Plugin Name: Lottie Animations
	Description: Include Lotties with shortcodes
	Author: Absatzformat GmbH
	Version: 1.0.0
	Author URI: https://absatzformat.de
*/

namespace Absatzformat\Wordpress\LottieAnimations;

defined('WPINC') or die();

define(__NAMESPACE__ . '\PLUGIN_VERSION',	'1.0.0');
define(__NAMESPACE__ . '\PLUGIN_PATH',		plugin_dir_path(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_URL',		plugin_dir_url(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_SLUG',		pathinfo(__FILE__, PATHINFO_FILENAME));
define(__NAMESPACE__ . '\MENU_SLUG',		PLUGIN_SLUG);


final class LottieAnimations
{

	private static $instance = null;
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function init()
	{
		return self::getInstance();
	}

	private function __construct()
	{
		if (!is_admin()) {

			add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
			add_shortcode('af-lottie-animation', [$this, 'handleShortcode']);
		}
	}

	public function registerScripts()
	{
		// wp_register_style('lottie-animations', PLUGIN_URL . 'assets/lottie-animations.css');
		wp_register_script('lottie-light', 'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.5/lottie_light.min.js', [], null, true);
	}

	public function enqueueScripts()
	{
		// wp_enqueue_style('lottie-animations');
		wp_enqueue_script('lottie-light');
	}

	public function handleShortcode($attrs = [], $content = null)
	{

		// load scripts
		$this->enqueueScripts();

		$containerClass = uniqid('af-lottie-');

		$defaultOptions = [
			'name' => $containerClass,
			'renderer' => 'svg',
			'loop' => true,
			'autoplay' => true,
			'path' => PLUGIN_URL . 'assets/example.json'
		];

		// extend options
		$options = shortcode_atts($defaultOptions, $attrs, 'af-lottie-animation');

		$options['container'] = '%selector%';
		$options['loop'] = filter_var($options['loop'], FILTER_VALIDATE_BOOLEAN);
		$options['autoplay'] = filter_var($options['autoplay'], FILTER_VALIDATE_BOOLEAN);

		$optionsJson = json_encode($options);
		$optionsJson = str_replace('"%selector%"', "document.querySelector('.$containerClass')", $optionsJson);

		wp_add_inline_script('lottie-light', "lottie.loadAnimation($optionsJson);");

		return <<<HTML
			<div class="$containerClass"></div>
		HTML;
	}
}

LottieAnimations::init();
