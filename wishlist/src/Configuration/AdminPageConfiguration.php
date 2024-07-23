<?php

namespace Dits\CaW\Configuration;

use Dits\CaW\Admin\AdminPage;
use Dits\CaW\DependencyInjection\Container;

class AdminPageConfiguration implements ConfigurationInterface {

    /**
     * @inheritDoc
     */
    public function modify( Container $container ) {
        $container['admin_page'] = $container->service( function ( Container $container ) {
            return new AdminPage( $container['settings'], $container['admin_fields'], $container['plugin_basename'] );
        } );
    }
}
