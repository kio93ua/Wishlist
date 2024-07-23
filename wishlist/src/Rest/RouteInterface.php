<?php

namespace Dits\CaW\Rest;

interface RouteInterface {

    public function get_path(): string;

    /**
     * Get the expected arguments for the REST API endpoint.
     *
     * @return array
     */
    public function get_arguments(): array;

    /**
     * Get the callback used by the REST API endpoint.
     *
     */
    public function get_callback(): callable;

    /**
     * Get the callback used to validate a request to the REST API endpoint.
     *
     */
    public function get_permission_callback(): callable;

    /**
     * Get the HTTP methods that the REST API endpoint responds to.
     *
     */
    public function get_methods(): string;
}
