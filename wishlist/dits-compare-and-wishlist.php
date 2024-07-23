<?php
/**
 * Plugin Name:       git statusWishlist
 * Plugin URI:        https://github.com/Cle-Menza/dits-compare-and-wishlist
 * Description:       Woocommerce Compare and Wishlist with webpack build script, composer autoloader and namespacing
 * Version:           1.0.0
 * Author:            TheMkvz, Dits.md
 * Author URI:        https://github.com/Cle-Menza
 * Contributors:
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       dits
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
use Dits\CaW\Activator;

defined( 'WPINC' ) || die;

global $dcaw_plugin;

require __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, [ Activator::class, 'activate' ] );

$dcaw_plugin = new Dits\CaW\Plugin( __FILE__ );

add_action( 'plugins_loaded', [ $dcaw_plugin, 'load' ] );
