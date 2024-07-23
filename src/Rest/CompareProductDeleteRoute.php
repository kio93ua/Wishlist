<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Compare;

class CompareProductDeleteRoute extends AbstractRoute {

    private Compare $compare;

    public function __construct( Compare $compare ) {
        $this->compare = $compare;
    }

    public function get_path(): string {
        return 'compare/product/(?P<id>\d+)';
    }

    public function get_arguments(): array {
        return [
            'id' => [
                'description'       => __( 'Unique identifier for the product.' ),
                'type'              => 'integer',
                'required'          => true,
                'sanitize_callback' => function ( $param ) {
                    return absint( $param );
                },
            ]
        ];
    }

    public function get_methods(): string {
        return 'DELETE';
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ): \WP_REST_Response {
        $product_id = $request->get_param( 'id' );

        $current_data = $this->compare->get_data();

        array_walk( $current_data, function ( &$list ) use ( $product_id ) {
            $key = array_search( $product_id, $list['products'] );

            if ( $key !== false ) {
                array_splice( $list['products'], $key, 1 );
            }
        } );

        $this->compare->set_data( $current_data );

        $product_title = get_the_title( $product_id );

        return new OkResponse( $this->compare->get_data(), sprintf( __( 'Product %s removed from compare', 'dits' ), $product_title ) );
    }
}
