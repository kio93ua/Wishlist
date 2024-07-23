<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistRoute extends AbstractRoute {

    protected Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist';
    }

    public function get_methods(): string {
        return 'GET';
    }

    public function get_arguments(): array {
        return [];
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        $filtered_data = [
            $this->wishlist->get_default_list()
        ];

        return new OkResponse( $filtered_data, __( 'Products loaded', 'dits' ) );
    }
}
