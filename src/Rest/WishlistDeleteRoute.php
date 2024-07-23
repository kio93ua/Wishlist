<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Wishlist;

class WishlistDeleteRoute extends AbstractRoute {

    protected Wishlist $wishlist;

    public function __construct( Wishlist $wishlist ) {
        $this->wishlist = $wishlist;
    }

    public function get_path(): string {
        return 'wishlist/list/(?P<id>[a-zA-Z0-9]+)';
    }

    public function get_methods(): string {
        return 'DELETE';
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
        return is_user_logged_in();
    }

    public function respond( \WP_REST_Request $request ): OkResponse {
        $list_id = $request->get_param( 'id' );

        $data    = $this->wishlist->get_data();
        $default = false;

        $data = array_filter( $data, function ( $list ) use ( $list_id, &$default ) {
            if ( $list['id'] === $list_id && $list['default'] ) {
                $default = true;
            }

            if ( $list['id'] !== $list_id ) {
                return true;
            }
        } );

        if ( $default && ! empty( $data ) ) {
            $first_key                     = array_key_first( $data );
            $data[ $first_key ]['default'] = true;
        }

        $this->wishlist->set_data( $data );

        return new OkResponse( [ $this->wishlist->get_default_list() ], __( 'List deleted', 'dits' ) );
    }
}
