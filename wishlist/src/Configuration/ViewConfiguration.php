<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\DependencyInjection\Container;
use Dits\CaW\View;

class ViewConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['view'] = $container->service( function ( Container $container ) {
            return new View( $container['plugin_path'] );
        } );
    }
}
