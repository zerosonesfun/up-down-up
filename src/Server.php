<?php
namespace Defiant;

/**
 * Class Server
 *
 * Registers AJAX routes for handling plugin actions
 *
 * @package Defiant
 */
class Server {

	protected static $routes = [
		'fetch_scores'	=> 'FetchScores',
		'update_score'	=> 'UpdateScore'
	];

	/**
	 * Registers all AJAX routes
	 */
	public static function register() {
		foreach (static::$routes as $name => $method) {
			static::registerRoute($name, $method);
		}
	}

	/**
	 * Updates a posts score
	 */
	public static function UpdateScore() {
		$postLike = new PostLike($_POST['post_id']);
		$postLike->updateScore($_POST['score']);

		static::respond(array(
			'total_score'	=> $postLike->getTotalScore()
		));
	}

	/**
	 * Fetches total post score, and users current score
	 */
	public static function FetchScores() {
		$postLike = new PostLike($_POST['post_id']);

		static::respond(array(
			'total_score'	=> $postLike->getTotalScore(),
			'user_score'	=> $postLike->getUserScore()
		));
	}

	/**
	 * Responds with json to client
	 * @param $data
	 */
	public static function respond($data) {
		echo json_encode($data);
		die();
	}

	/**
	 * Registers an individual route
	 * @param $name
	 * @param $method
	 */
	protected static function registerRoute($name, $method) {
		add_action("wp_ajax_{$name}", array('Defiant\Server', $method));
	}
}
