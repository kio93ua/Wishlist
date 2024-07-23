<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Assets;
use Dits\CaW\DependencyInjection\Container;

class AssetsConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['assets'] = $container->service( function ( Container $container ) {
            return new Assets( $container['plugin_version'], $container['plugin_url'], $container['plugin_dir'], $container['compare'], $container['wishlist'] );
        } );
    }
}
