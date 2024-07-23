<?php

namespace Dits\CaW\DependencyInjection;

use Dits\CaW\Configuration\ConfigurationInterface;

class Container implements \ArrayAccess {
    /**
     * Flag that checks if the container is locked or not.
     *
     * @var bool
     */
    private bool $locked;

    /**
     * Values stored inside the container.
     *
     * @var array
     */
    private array $values;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct( array $values = [] ) {
        $this->locked = false;
        $this->values = $values;
    }

    /**
     * Configure the container using the given container configuration object(s).
     *
     * @param mixed $configurations
     */
    public function configure( $configurations ) {
        if ( ! is_array( $configurations ) ) {
            $configurations = [ $configurations ];
        }

        foreach ( $configurations as $configuration ) {
            $this->modify( $configuration );
        }
    }

    /**
     * Checks if the container is locked or not.
     *
     * @return bool
     */
    public function is_locked(): bool {
        return $this->locked;
    }

    /**
     * Locks the container so that it can't be modified.
     */
    public function lock() {
        $this->locked = true;
    }

    public function get_values(): array {
        return $this->values;
    }

    public function offsetExists( $offset ): bool {
        return array_key_exists( $offset, $this->values );
    }

    public function offsetGet( $offset ) {
        if ( ! array_key_exists( $offset, $this->values ) ) {
            throw new \InvalidArgumentException( sprintf( 'Container doesn\'t have a value stored for the "%s" key.', $offset ) );
        } elseif ( ! $this->is_locked() ) {
            $this->lock();
        }

        return $this->values[ $offset ] instanceof \Closure ? $this->values[$offset]( $this ) : $this->values[ $offset ];
    }

    public function offsetSet( $offset, $value ) {
        if ( $this->locked ) {
            throw new \RuntimeException( 'Container is locked and cannot be modified.' );
        }

        $this->values[ $offset ] = $value;
    }

    public function offsetUnset( $offset ) {
        if ( $this->locked ) {
            throw new \RuntimeException( 'Container is locked and cannot be modified.' );
        }

        unset( $this->values[ $offset ] );
    }

    /**
     * Creates a closure used for creating a service using the given callable.
     *
     * @param callable $callback
     *
     * @return callable
     */
    public function service( callable $callback ) {
        if ( ! is_object( $callback ) || ! method_exists( $callback, '__invoke' ) ) {
            throw new \InvalidArgumentException( 'Service definition is not a Closure or invokable object.' );
        }

        return function ( Container $container ) use ( $callback ) {
            static $object;

            if ( null === $object ) {
                $object = $callback( $container );
            }

            return $object;
        };
    }

    /**
     * Modify the container using the given container configuration object.
     *
     * @param mixed $configuration
     */
    private function modify( $configuration ) {
        if ( is_string( $configuration ) ) {
            $configuration = new $configuration();
        }

        if ( ! $configuration instanceof ConfigurationInterface ) {
            throw new \InvalidArgumentException( 'Configuration object must implement the "ContainerConfigurationInterface".' );
        }

        $configuration->modify( $this );
    }
}
