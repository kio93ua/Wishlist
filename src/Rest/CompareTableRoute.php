<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Compare;

class CompareTableRoute extends AbstractRoute {

    private Compare $compare;

    public function __construct( Compare $compare ) {
        $this->compare = $compare;
    }

    public function get_path(): string {
        return 'compare/table';
    }

    public function get_arguments(): array {
        return [];
    }

    public function get_methods(): string {
        return 'GET';
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        return new OkResponse( $this->compare->table() );
    }
}
