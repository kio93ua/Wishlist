<?php

namespace Dits\CaW;

// Exit if accessed directly
defined( 'WPINC' ) || die;

use Dits\CaW\Admin\Settings;
use Dits\CaW\Configuration\ActivatorConfiguration;
use Dits\CaW\Configuration\AdminPageConfiguration;
use Dits\CaW\Configuration\AssetsConfiguration;
use Dits\CaW\Configuration\FieldsConfiguration;
use Dits\CaW\Configuration\I18nConfiguration;
use Dits\CaW\Configuration\RestApiConfiguration;
use Dits\CaW\Configuration\SettingsConfiguration;
use Dits\CaW\Configuration\ViewConfiguration;
use Dits\CaW\Configuration\WishlistConfiguration;
use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\Utils\HooksInterface;

/**
 * @file
 *
 * The core plugin class.
 */
class Plugin {
    const DOMAIN = 'dits';

    const VERSION = '1.0.0';

    private Container $container;

    private bool $loaded;

    public function __construct( $file ) {
        $this->container = new Container( [
            'plugin_basename' => plugin_basename( $file ),
            'plugin_domain'   => self::DOMAIN,
            'plugin_path'     => plugin_dir_path( $file ),
            'plugin_dir'      => basename( plugin_dir_path( $file ) ),
            'plugin_url'      => plugin_dir_url( $file ),
            'plugin_version'  => self::VERSION,
        ] );

        $this->loaded = false;
    }

    public function load() {
        if ( $this->loaded ) {
            return;
        }

        $this->container->configure( [
            ActivatorConfiguration::class,
            AdminPageConfiguration::class,
            SettingsConfiguration::class,
            ViewConfiguration::class,
            AssetsConfiguration::class,
            I18nConfiguration::class,
            RestApiConfiguration::class,
            WishlistConfiguration::class,
            FieldsConfiguration::class,
        ] );

        foreach ( $this->container->get_values() as $key => $value ) {
            if ( isset($this->container[ $key ]) && $this->container[ $key ] instanceof HooksInterface) {
                $this->container[ $key ]->hooks();
            }
        }

        $this->loaded = true;
    }

    /**
     * @return Settings
     */
    public function get_settings(): Settings {
        return $this->container['settings'];
    }

    /**
     * @return View
     */
    public function get_view(): View {
        return $this->container['view'];
    }
}