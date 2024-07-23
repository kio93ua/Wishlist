<?php

namespace Dits\CaW\Modules;

class Compare extends AbstractModule {
    protected string $meta_key = 'dcaw_compared_products';

    function get_module_name(): string {
        return 'compare';
    }

    public function get_config(): array {
        return [
            'selectors' => [
                'counters' => '.js-dcaw-compare-count',
                'button'   => '.js-dcaw-compare-btn',
                'remove'   => '.js-dcaw-compare-remove-btn',
                'collapse' => '.js-dcaw-attribute-collapse',
                'tables'   => '.dcaw-compare'
            ],
            'classes'   => [
                'counter'  => 'dcaw-compare-count',
                'empty'    => 'is-empty',
                'active'   => 'is-active',
                'loading'  => 'is-loading',
                'collapse' => 'is-collapse'
            ],
            'max'       => $this->settings->get( 'max', 'compare' ),
        ];
    }

    public function hooks() {
        parent::hooks();

        // Compare shortcode
        add_shortcode( 'dcaw_compare_table', [ $this, 'table_shortcode' ] );
    }

    public function button( $atts = [] ): string {
        $args = $this->get_button_args( $atts['id'] ?? null );

        $current_data = $this->get_data();

        foreach ( $current_data as $list ) {
            if ( in_array( $args['id'], $list['products'] ) ) {
                $args['class'] .= ' is-active';
            }
        }

        return $this->view->get( 'compare-button.twig', $args );
    }

    public function table_shortcode(): string {
        $args['content'] = $this->table();

        return $this->view->get( 'compare.twig', $args );
    }

    public function table(): string {
        $html = '';

        foreach ( $this->get_data() as $list ) {
            $products = [];

            if ( empty( $list['products'] ) ) {
                $args['empty_content'] = $this->view->get( 'empty-content.twig', [
                    'svg'         => $this->view->get( 'icons/empty.svg' ),
                    'title'       => __( 'Compare is empty', 'dits' ),
                    'text'        => __( 'Fill it with goods', 'dits' ),
                    'button_text' => __( 'Go to shop', 'dits' ),
                    'button_link' => \get_permalink( \get_option( 'woocommerce_shop_page_id' ) )
                ] );
            } else {
                foreach ( $list['products'] as $product_id ) {
                    $_product = wc_get_product( $product_id );

                    // Get product info
                    $products[ $product_id ] = [
                        'img'        => $_product->get_image(),
                        'title'      => $_product->get_name(),
                        'link'       => $_product->get_permalink(),
                        'price'      => $_product->get_price_html(),
                        'buy'        => do_shortcode( '[add_to_cart id="' . $product_id . '" show_price="false" style=""]' ),
                        'compare'    => $this->button( [ 'id' => $product_id ] ),
                        'attributes' => []
                    ];

                    // Parse all product attributes
                    if ( $this->settings->get( 'attributes', 'compare' ) ) {
                        foreach ( $_product->get_attributes() as $slug => $attribute_object ) {
                            $products[ $product_id ]['attributes'][ $slug ] = [
                                'name'  => wc_attribute_label( $attribute_object->get_name(), $_product ),
                                'value' => $_product->get_attribute( $slug ),
                            ];
                        }
                    }

                    // Parse taxonomies
                    if ( $taxonomies = $this->settings->get( 'taxonomies', 'compare' ) ) {
                        foreach ( $taxonomies as $taxonomy ) {
                            $terms = get_the_terms( $product_id, $taxonomy );

                            if ( ! $terms ) {
                                continue;
                            }

                            $terms = array_map( function ( $term ) {
                                return $term->name;
                            }, $terms );

                            $products[ $product_id ]['attributes'][ $taxonomy ] = [
                                'name'  => get_taxonomy( $taxonomy )->label,
                                'value' => implode( ', ', $terms ),
                            ];
                        }
                    }

                    // Parse dimensions
                    if ( $dimensions = $this->settings->get( 'dimensions', 'compare' ) ) {
                        $dimensions_unit = get_option( 'woocommerce_dimension_unit' );
                        $weight_unit     = get_option( 'woocommerce_weight_unit' );

                        foreach ( $dimensions as $dimension ) {
                            $method_name     = 'get_' . $dimension;
                            $dimension_value = $_product->$method_name();
                            $unit            = $dimension === 'weight' ? $weight_unit : $dimensions_unit;

                            if ( ! $dimension_value ) {
                                continue;
                            }

                            $products[ $product_id ]['attributes'][ $dimension ] = [
                                'name'  => __( ucfirst( $dimension ), 'dits' ),
                                'value' => sprintf( '%s %s', $dimension_value, $unit )
                            ];
                        }
                    }
                }
            }

            $args['attributes'] = $this->prepare_attributes( $products );
            $args['products']   = $products;
            $args['count']      = count( $products );
            $args['title']      = sprintf( __( 'Compare %s products', 'dits' ), count( $products ) );
            $args['clear_text'] = __( 'Clear', 'dits' );
            $args['clear_icon'] = $this->view->get( 'icons/trash.svg' );

            $html .= $this->view->get( 'compare-table.twig', $args );
        }

        return $html;
    }

    public function set_data( array $data ) {
        if ( $user_id = get_current_user_id() ) {
            update_user_meta( $user_id, $this->meta_key, $data );
        } else {
            $_SESSION[ $this->meta_key ] = $data;
        }

        $this->data = $data;
    }

    public function get_default_data(): array {
        return [
            [ 'id' => 0, 'products' => [] ]
        ];
    }

    public function load_data(): void {
        $data = $this->get_default_data();

        if ( $user_id = get_current_user_id() ) {
            $user_meta = get_user_meta( $user_id, $this->meta_key, true );
            $data      = $user_meta ?: $data;
        } else {

            if ( isset( $_SESSION[ $this->meta_key ] ) && ! empty( $_SESSION[ $this->meta_key ] ) ) {
                $data = $_SESSION[ $this->meta_key ];
            }

        }

        $this->set_data( $data );
    }

    private function prepare_attributes( array $products ): array {
        $attrs_keys = $attributes = [];

        foreach ( $products as $product ) {
            $attrs_keys = array_merge( $attrs_keys, array_keys( $product['attributes'] ) );
        }

        $attrs_keys = array_values( array_unique( $attrs_keys ) );

        foreach ( $attrs_keys as $attr_slug ) {
            foreach ( $products as $id => $product ) {
                $exist_attr = isset( $product['attributes'][ $attr_slug ] );

                if ( $exist_attr ) {
                    $attributes[ $attr_slug ]['name'] = $product['attributes'][ $attr_slug ]['name'];
                }

                $attributes[ $attr_slug ]['values'][ $id ]['product'] = $product['title'];
                $attributes[ $attr_slug ]['values'][ $id ]['value']   = $exist_attr
                    ? $product['attributes'][ $attr_slug ]['value']
                    : false;
            }
        }

        return array_map( function ( $attribute ) {
            $all_values = array_map( function ( $value ) {
                return $value['value'];
            }, $attribute['values'] );

            if ( count( array_unique( $all_values ) ) === 1 ) {
                $attribute['identical'] = true;
            }

            return $attribute;
        }, $attributes );
    }
}
