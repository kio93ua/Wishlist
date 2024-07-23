<?php

namespace Dits\CaW\Admin;

// Exit if accessed directly
use Dits\CaW\Utils\HooksInterface;

defined( 'WPINC' ) || die;

class AdminPage implements HooksInterface {
    protected Settings $settings;

    protected Fields $fields;

    protected string $plugin_basename;

    public function __construct( Settings $settings, Fields $fields, $plugin_basename ) {
        $this->settings        = $settings;
        $this->fields          = $fields;
        $this->plugin_basename = $plugin_basename;
    }

    public function get_menu_title(): string {
        return __( 'Dits Compare & Wishlist', 'dits' );
    }

    public function get_page_title(): string {
        return __( 'Dits Compare and Wishlist Settings', 'dits' );
    }

    public function get_plugins_page_title(): string {
        return __( 'Settings', 'dits' );
    }

    public function get_slug(): string {
        return 'dits-compare-and-wishlist';
    }

    public function get_capability(): string {
        return 'install_plugins';
    }

    public function get_parent_slug(): string {
        return '';
    }

    public function get_page_url(): string {
        return admin_url( $this->get_parent_slug() ) . '?page=' . $this->get_slug();
    }

    public function admin_page() {
        echo '<div class="wrap">';

        echo "<h1>{$this->get_page_title()}</h1>";

        $this->settings->show_navigation();
        $this->settings->show_forms();

        echo '</div>';
    }

    public function admin_init() {
        $this->settings->set_sections( $this->fields->get_sections() );
        $this->settings->set_fields( $this->fields->get_fields() );

        $this->settings->admin_init();
    }

    /**
     * Adds the plugin's admin page to the menu.
     */
    public function admin_menu() {
        if ( $this->get_parent_slug() ) {
            add_submenu_page(
                $this->get_parent_slug(),
                $this->get_page_title(),
                $this->get_menu_title(),
                $this->get_capability(),
                $this->get_slug(),
                [ $this, 'admin_page' ] );
        } else {
            add_menu_page(
                $this->get_page_title(),
                $this->get_menu_title(),
                $this->get_capability(),
                $this->get_slug(),
                [ $this, 'admin_page' ]
            );
        }
    }

    /**
     * Adds link from plugins page to admin page.
     *
     * @param array $actions
     *
     * @return array
     */
    public function add_plugin_page_link( array $actions ): array {
        array_unshift( $actions, "<a href=\"{$this->get_page_url()}\">{$this->get_plugins_page_title()}</a>" );

        return $actions;
    }

    public function hooks() {
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_filter( 'plugin_action_links_' . $this->plugin_basename, [ $this, 'add_plugin_page_link' ] );
        add_action( 'add_option', [ $this, 'save_options' ] );
        add_action( 'update_option', [ $this, 'save_options' ] );
    }

    public function save_options( $option ) {
        if ( strpos( $option,  $this->get_slug() ) !== false ) {
            \flush_rewrite_rules();
        }
    }
}
