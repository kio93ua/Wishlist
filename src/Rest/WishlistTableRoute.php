<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistTableRoute extends AbstractRoute {

    private Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/table';
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
        return new OkResponse( $this->wishlist->table() );
    }
}
