<?php
/**
 * @package Up Down Up
 * @version 1.0.0
 */
/*
Plugin Name: Up Down Up
Plugin URI: N/A
Description: Adds up and down voting buttons to posts.
Author: Billy Wilcosky
Version: 1.0.0
Author URI: https://wilcosky.com
*/

require __DIR__ . '/vendor/autoload.php';

use Defiant\Client;
use Defiant\Server;

register_activation_hook( __FILE__, array('Defiant\PostLike', 'install'));

Client::register();
Server::register();