<?php

namespace Dits\CaW;

use Dits\CaW\Modules\Compare;
use Dits\CaW\Modules\Wishlist;
use Dits\CaW\Utils\HooksInterface;

class Assets implements HooksInterface {
    protected string $plugin_version;
    protected string $plugin_url;
    protected string $plugin_dir;
    protected ?Compare $compare;
    protected ?Wishlist $wishlist;
    protected string $min;

    public function __construct( $plugin_version, $plugin_url, $plugin_dir, $compare, $wishlist ) {
        $this->plugin_version = $plugin_version;
        $this->plugin_url     = $plugin_url;
        $this->plugin_dir     = $plugin_dir;
        $this->compare        = $compare;
        $this->wishlist       = $wishlist;

        $this->min = WP_DEBUG ? '' : '.min';
    }

    public function hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'front_assets' ] );
    }

    public function admin_assets() {
        wp_enqueue_style(
            $this->plugin_dir . '-admin',
            $this->plugin_url . 'assets/dist/admin' . $this->min . '.css',
            [],
            $this->plugin_version
        );

        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_script(
            $this->plugin_dir . '-admin',
            $this->plugin_url . 'assets/dist/admin' . $this->min . '.js',
            [],
            $this->plugin_version,
            true
        );

        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );

        wp_enqueue_media();
    }

    public function front_assets() {
        wp_enqueue_style(
            $this->plugin_dir . '-front',
            $this->plugin_url . 'assets/dist/main' . $this->min . '.css',
            [],
            $this->plugin_version
        );

        wp_enqueue_script(
            $this->plugin_dir . '-front',
            $this->plugin_url . 'assets/dist/main' . $this->min . '.js',
            [],
            $this->plugin_version,
            true
        );

        wp_localize_script( $this->plugin_dir . '-front', 'dcawGeneral', [
            'rest'  => get_rest_url() . $this->plugin_dir,
            'nonce' => wp_create_nonce( 'wp_rest' ),
        ] );

        if ( $this->compare ) {
            $data = $this->compare->get_config();

            if ( $selector = $this->compare->settings->get( 'count_selector', 'compare' ) ) {
                $data['selectors']['counters'] = implode( ',', [ $selector, $data['selectors']['counters'] ] );
            }

            wp_localize_script( $this->plugin_dir . '-front', 'dcawCompare', $data );
        }

        if ( $this->wishlist ) {
            $data = $this->wishlist->get_config();

            if ( $selector = $this->wishlist->settings->get( 'count_selector', 'wishlist' ) ) {
                $data['selectors']['counters'] = implode( ',', [ $selector, $data['selectors']['counters'] ] );
            }

            wp_localize_script( $this->plugin_dir . '-front', 'dcawWishlist', $data );
        }

    }
}
