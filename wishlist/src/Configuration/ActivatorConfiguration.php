<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Activator;
use Dits\CaW\DependencyInjection\Container;

class ActivatorConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['activator'] = $container->service( function ( Container $container ) {
            return new Activator( $container['plugin_basename'] );
        } );
    }
}
