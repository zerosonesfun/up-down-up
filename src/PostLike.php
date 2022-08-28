<?php
namespace Defiant;


/**
 * Class PostLike
 *
 * Handles database operations for post scores
 *
 * @package Defiant
 */
class PostLike
{
	protected static $tableName = 'defiant_post_scores';
	protected $PostId;

	function __construct($postId) {
		$this->PostId = $postId;
	}

	/**
	 * Upserts score based on client IP and post ID
	 * @param int $score
	 */
	public function updateScore($score) {
		global $wpdb;
		$currentScore = $this->getUserScore();
		if ($currentScore === 0) {
			$wpdb->insert(static::getTableName(), array(
				'post_id' 		=> $this->PostId,
				'ip_address'	=> $this->getIP(),
				'score'			=> $score
			));
		} elseif ($currentScore !== $score) {
			$wpdb->update(static::getTableName(), array(
				'score'			=> $score
			), array(
				'post_id'		=> $this->PostId,
				'ip_address'	=> $this->getIP()
			));
		}
	}

	/**
	 * Fetches score for current client IP and Post ID
	 * @return int
	 */
	public function getUserScore() {
		global $wpdb;
		$tableName = static::getTableName();
		$sql = <<<SQL
			SELECT score
			FROM {$tableName}
			WHERE ip_address = %s
				AND post_id = %s
SQL;
		$params = array($this->getIP(), $this->PostId);
		$results = $wpdb->get_results(
			$wpdb->prepare($sql, $params)
		);
		return empty($results) ? 0 : (int)$results[0]->score;
	}

	/**
	 * Gets aggregated score for the current post
	 * @return int
	 */
	public function getTotalScore() {
		global $wpdb;
		$tableName = static::getTableName();
		$sql = <<<SQL
			SELECT COALESCE(SUM(score), 0) AS score
			FROM {$tableName}
			WHERE post_id = %s
SQL;
		$results = $wpdb->get_results(
			$wpdb->prepare($sql, array($this->PostId))
		);
		return (int)$results[0]->score;
	}

	/**
	 * Creates database table for post scores
	 */
	public static function install() {
		global $wpdb;
		$wpdb->show_errors();

		$tableName = static::getTableName();

		$charsetCollate = $wpdb->get_charset_collate();

		$sql = <<<SQL
			CREATE TABLE {$tableName} (
				id MEDIUMINT(9) NOT NULL  AUTO_INCREMENT,
				post_id MEDIUMINT(9),
				ip_address VARBINARY(16) NOT NULL,
				score INT NOT NULL,
				created_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				updated_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charsetCollate};
			
			CREATE INDEX def_pl_post_id_x ON {$tableName}(post_id);
SQL;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * Fully qualified database table name, including WordPress prefix
	 * @return string
	 */
	protected static function getTableName() {
		global $wpdb;
		return $wpdb->prefix . static::$tableName;
	}

	/**
	 * Client IP address
	 * @return mixed
	 */
	private function getIP() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
}
