<?php

namespace Dits\CaW;

// Exit if accessed directly.
use Dits\CaW\Utils\HooksInterface;

defined( 'WPINC' ) || die;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class Activator implements HooksInterface {
    public string $plugin_basename;

    const KEY = 'dcaw_activated';

    public function __construct( $plugin_basename ) {
        $this->plugin_basename = $plugin_basename;
    }

    public function hooks() {
        add_action( 'admin_notices', [ $this, 'admin_notices' ] );
        add_action( 'admin_init', [ $this, 'deactivate_plugin' ] );
    }

    public function deactivate_plugin() {
        if ( ! self::compatible_version() ) {
            deactivate_plugins( $this->plugin_basename );
        }
    }

    public function admin_notices() {
        if ( get_transient( self::KEY ) ) {
            ?>
            <div class="updated notice is-dismissible">
                <p>Thank you for using DITS Wishlist! <strong>You are awesome</strong>.</p>
            </div>
            <?php
            delete_transient( self::KEY );
        }

        if ( ! self::compatible_version() ) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><strong>DITS Wishlist</strong> requires WordPress 5.0 or higher!</p>
            </div>
            <?php

            if ( isset( $_GET['activate'] ) ) {
                unset( $_GET['activate'] );
            }
        }
    }

    public static function compatible_version(): bool {
        $wp_version = '5.0';

        if ( version_compare( $GLOBALS['wp_version'], $wp_version, '<' ) ) {
            return false;
        }

        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            return false;
        }

        return true;
    }

    public static function activate() {
        set_transient( self::KEY, true, 5 );
    }
}
