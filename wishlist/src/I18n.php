<?php

namespace Dits\CaW;

use Dits\CaW\Utils\HooksInterface;

class I18n implements HooksInterface {
    private string $domain;

    private string $plugin_dir;

    /**
     * Constructor.
     *
     * @param string $domain
     * @param string $plugin_dir
     */
    public function __construct( string $domain, string $plugin_dir ) {
        $this->domain     = $domain;
        $this->plugin_dir = $plugin_dir;
    }

    public function hooks() {
        add_action( 'plugins_loaded', function () {
            load_plugin_textdomain(
                $this->domain,
                false,
                $this->plugin_dir . '/languages/'
            );
        }, 20 );
    }
}
