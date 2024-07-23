<?php

namespace Dits\CaW\Rest;

use Dits\CaW\Utils\HooksInterface;

class RestApi implements HooksInterface {

    private string $namespace;

    private array $routes = [];

    public function __construct( $namespace, $routes ) {
        $this->namespace = $namespace;

        foreach ( $routes as $route ) {
            $this->add_route( $route );
        }
    }

    public function hooks() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function add_route( RouteInterface $route ) {
        $this->routes[] = $route;
    }

    public function register_routes() {
        foreach ( $this->routes as $route ) {
            $this->register_route( $route );
        }
    }

    private function register_route( RouteInterface $route ) {
        register_rest_route( $this->namespace, $route->get_path(), [
            'args'                => $route->get_arguments(),
            'callback'            => $route->get_callback(),
            'methods'             => $route->get_methods(),
            'permission_callback' => $route->get_permission_callback(),
        ] );
    }
}
