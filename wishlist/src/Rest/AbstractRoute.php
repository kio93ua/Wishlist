<?php

namespace Dits\CaW\Rest;

abstract class AbstractRoute implements RouteInterface {
    final public function get_callback(): callable {
        return [ $this, 'respond' ];
    }

    abstract public function respond( \WP_REST_Request $request );

    final public function get_permission_callback(): callable {
        return [ $this, 'permission' ];
    }

    abstract public function permission(): bool;
}
