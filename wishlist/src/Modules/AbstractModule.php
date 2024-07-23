<?php

namespace Dits\CaW\Modules;

use Dits\CaW\Admin\Settings;
use Dits\CaW\Utils\HooksInterface;
use Dits\CaW\View;

abstract class AbstractModule implements HooksInterface {
    public Settings $settings;
    public View $view;
    protected string $plugin_dir;
    protected array $data = [];

    public function __construct( Settings $settings, View $view, $plugin_dir ) {
        $this->settings   = $settings;
        $this->view       = $view;
        $this->plugin_dir = $plugin_dir;
    }

    public function start_session() {
        if ( ! session_id() && ! headers_sent() ) {
            session_start();
        }
    }

    public function hooks() {
        add_action( 'init', [ $this, 'start_session' ], 10 );
        add_action( 'init', [ $this, 'load_data' ], 20 );

        $button_single_hook = $this->settings->get( 'single_button_hook', $this->get_module_name() );
        $button_loop_hook   = $this->settings->get( 'loop_button_hook', $this->get_module_name() );

        // Single page button
        if ( ! empty( $button_single_hook ) && $button_single_hook !== 'manual' ) {
            add_action( $button_single_hook, function () {
                echo $this->button();
            } );
        } else {
            add_shortcode( 'dcaw_' . $this->get_module_name() . '_button', [ $this, 'button' ] );
        }

        // Archive page button
        if ( ! empty( $button_loop_hook ) && $button_loop_hook !== 'manual' ) {
            add_filter( 'woocommerce_loop_add_to_cart_link', function ( $add_to_cart_html, $product ) use ( $button_loop_hook ) {
                $button = $this->button( [ 'id' => $product->get_id() ] );

                return $button_loop_hook === 'before' ? $button . $add_to_cart_html : $add_to_cart_html . $button;
            }, 10, 2 );
        } else {
            add_shortcode( 'dcaw_' . $this->get_module_name() . '_button', [ $this, 'button' ] );
        }
    }

    public function get_button_args( $product_id = null ): array {
        if ( ! $product_id ) {
            global $post;

            $product_id = $post->ID;
        }

        return [
            'id'    => $product_id,
            'title' => get_the_title( $product_id ),
            'class' => $this->settings->get( 'button_class', $this->get_module_name() ),
            'text'  => $this->settings->get( 'button_text', $this->get_module_name() ),
            'icon'  => $this->settings->get( 'button_icon', $this->get_module_name() )
        ];
    }

    public function get_data(): array {
        return $this->data;
    }

    abstract public function set_data( array $data );

    abstract public function load_data(): void;

    abstract public function get_default_data(): array;

    abstract public function get_module_name(): string;

    abstract public function button( array $atts = [] );
}
