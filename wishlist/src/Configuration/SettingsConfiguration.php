<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Admin\Settings;
use Dits\CaW\DependencyInjection\Container;

class SettingsConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['settings'] = $container->service( function ( Container $container ) {
            return new Settings( $container['plugin_dir'] );
        } );
    }
}
