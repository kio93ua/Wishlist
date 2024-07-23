<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistShareRoute extends AbstractRoute {

    protected Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/list/share/(?P<id>[a-zA-Z0-9]+)';
    }

    public function get_methods(): string {
        return 'GET';
    }

    public function get_arguments(): array {
        return [
            'id' => [
                'description'       => __( 'Unique identifier for the list.' ),
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => function ( $param ) {
                    return htmlspecialchars( $param );
                },
            ]
        ];
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        $list_id = $request->get_param( 'id' );
        $user_id = get_current_user_id();

        $url = home_url( "/shared-wishlist/$user_id/$list_id" );

        return new OkResponse( $url, __( 'Link copied to clipboard', 'dits' ) );
    }
}
