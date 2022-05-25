<?php

namespace Absatzformat\Wordpress\LottieAnimations;

final class LottieAnimations
{
	/** @var null|LottieAnimations */
	public static $instance = null;

	/** @var string */
	protected static $shortcodeName = 'wp-lottie-animation';

	/** @var string */
	protected $pluginUrl;

	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected function __construct()
	{
		$this->pluginUrl = plugin_dir_url(__DIR__ . '/../..');

		if (!is_admin()) {

			add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
			add_shortcode(self::$shortcodeName, [$this, 'handleShortcode']);
		}
	}

	public function registerScripts(): void
	{
		wp_register_script('lottie-light', $this->pluginUrl . 'assets/js/lottie_light.min.js', [], null, true);
	}

	public function enqueueScripts(): void
	{
		wp_enqueue_script('lottie-light');
	}

	public function handleShortcode($attrs = []): string
	{
		// load scripts
		$this->enqueueScripts();

		$containerClass = uniqid('wp-lottie-animation-');

		$defaultOptions = [
			'name' => $containerClass,
			'renderer' => 'svg',
			'loop' => true,
			'autoplay' => true,
			'path' => $this->pluginUrl . 'assets/example.json'
		];

		// extend options
		$options = shortcode_atts($defaultOptions, $attrs, self::$shortcodeName);

		$options['container'] = '%selector%';
		$options['loop'] = filter_var($options['loop'], FILTER_VALIDATE_BOOLEAN);
		$options['autoplay'] = filter_var($options['autoplay'], FILTER_VALIDATE_BOOLEAN);

		$optionsJson = json_encode($options);
		$optionsJson = str_replace('"%selector%"', "document.querySelector('.$containerClass')", $optionsJson);

		wp_add_inline_script('lottie-light', "lottie.loadAnimation($optionsJson);");

		return '<div class="' . $containerClass . '"></div>';
	}
}
