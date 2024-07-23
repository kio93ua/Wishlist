<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Modules\Compare;

class CompareProductPostRoute extends AbstractRoute {

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
        return 'POST';
    }

    public function permission(): bool {
        return true;
    }

    public function respond( \WP_REST_Request $request ) {
        $max_count    = $this->compare->settings->get( 'max', 'compare' );
        $product_id   = $request->get_param( 'id' );
        $current_data = $this->compare->get_data();
        // FIXME
        $products_count = count( $current_data[0]['products'] );

        if ( $products_count >= $max_count ) {
            return new FailedResponse( 'max', __( 'Maximum product compared', 'dits' ) );
        }

        array_walk( $current_data, function ( &$list, $i ) use ( $product_id ) {
            // FIXME
            if ( $i === 0 ) {
                array_push( $list['products'], $product_id );
            }
        } );

        $this->compare->set_data( $current_data );

        $product_title = get_the_title( $product_id );

        return new OkResponse( $this->compare->get_data(), sprintf( __( 'Product %s added to compare', 'dits' ), $product_title ) );
    }
}
