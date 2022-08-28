<?php
namespace Defiant;

/**
 * Class Client
 *
 * Registers client side actions, javascript, and css
 *
 * @package Defiant
 */
class Client {

	protected static $scripts = [
		'ui_actions' => '/js/actions.js?v=5'
	];

	protected static $styles = [
		'font-awesome'	=> '/css/all.css',
		'ui_style'		=> '/css/style.css?v=1'
	];

	/**
	 * Registers all Styles, scripts, dom elements
	 */
	public static function register() {
		static::registerStyles();
		static::registerScripts();
		static::registerDOM();
	}

	/**
	 * Registers all javascript files
	 */
	protected function registerScripts() {
		foreach (static::$scripts as $handle => $scriptPath) {
			static::registerScript($handle, $scriptPath);
		}
	}

	/**
	 * Registers individual javascript file
	 * @param $handle
	 * @param $scriptPath
	 */
	protected static function registerScript($handle, $scriptPath) {
		add_action('wp_head', function() use($handle, $scriptPath) {
			wp_register_script( $handle, plugin_dir_url(__FILE__) . '../' . $scriptPath, array('jquery'));
			$data = array(
				'ajaxurl' 	=> admin_url('admin-ajax.php'),
				'post_id'	=> get_the_ID()
			);
			wp_localize_script( $handle, 'def', $data);
			wp_enqueue_script( $handle, false, array(), false, true);
		});
	}

	/**
	 * Registers all CSS files
	 */
	protected static function registerStyles() {
		foreach (static::$styles as $handle => $stylePath) {
			static::registerStyle($handle, $stylePath);
		}
	}

	/**
	 * Registers individual css file
	 */
	protected static function registerStyle($handle, $stylePath) {
		add_action('wp_head', function() use($handle, $stylePath) {
			wp_enqueue_style( $handle, plugin_dir_url(__FILE__) . '../' . $stylePath);
		});
	}

	/**
	 * Outputs DOM elements to browser footer
	 */
	protected static function registerDOM() {
		add_action('wp_footer', function() {
			if (is_single()) {
				$html = <<<HTML
					<div class="cream-fab-container">
						<div class="cream-fab-icon">
							<i class="fas fa-arrow-circle-up" id="cream-icon-up"></i>
						</div>
						<div class="cream-fab-counter">
							<p>0</p>
							<i class="fas fa-sync fa-spin"></i>
						</div>
						<div class="cream-fab-icon">
							<i class="fas fa-arrow-circle-down" id="cream-icon-down"></i>
						</div>
					</div>
HTML;
				echo $html;
			}
		});
	}
}
