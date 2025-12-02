<?php

namespace Blocks\Data;

/**
 * Abstract base class for iterable collections with sequential integer keys.
 * 
 * Provides a foundation for building custom collection types that implement
 * the Iterator and Countable interfaces. Subclasses must implement current()
 * to define how items are retrieved during iteration.
 * 
 * Collections automatically normalize array keys to sequential integers (0, 1, 2...)
 * to ensure consistent iteration behavior.
 */
abstract class Collection implements \Iterator, \Countable {
    /**
     * Current position in the collection during iteration.
     */
    protected int $position = 0;

    /**
     * Internal storage array with normalized sequential keys.
     */
    protected array $array = [];

    /**
     * Construct a new collection from an array of items.
     * 
     * Array keys are automatically reindexed to sequential integers.
     * 
     * @param array $items Initial items to populate the collection
     */
    public function __construct( array $items ) {
        $this->array = array_values( $items );
    }

    /**
     * Reset the iterator to the first element.
     */
    public function rewind(): void {
        $this->position = 0;
    }

    /**
     * Return the current element.
     * 
     * Subclasses must implement this method to define how items are retrieved.
     * 
     * Example default implementation:
     * <code>
     * public function current(): mixed {
     *     if ( !$this->valid() ) {
     *         throw new \OutOfBoundsException( 'Iterator position is invalid' );
     *     }
     *     return $this->array[$this->position];
     * }
     * </code>
     * 
     * @return mixed The current element
     */
    abstract public function current(): mixed;

    /**
     * Return the key of the current element.
     * 
     * @return int The current position (0-indexed)
     */
    public function key(): int {
        return $this->position;
    }

    /**
     * Move forward to the next element.
     */
    public function next(): void {
        ++$this->position;
    }

    /**
     * Check if the current position is valid.
     * 
     * Uses array_key_exists to properly handle null values in the collection.
     * 
     * @return bool True if the current position exists in the array
     */
    public function valid(): bool {
        return array_key_exists( $this->position, $this->array );
    }

    /**
     * Count the number of items in the collection.
     * 
     * @return int The number of items
     */
    public function count(): int {
        return count( $this->array );
    }

    /**
     * Add an item to the end of the collection.
     * 
     * @param mixed $item The item to add
     */
    public function add( mixed $item ): void {
        $this->array[] = $item;
    }

    /**
     * Remove an item at a specific index.
     * 
     * After removal, the array is reindexed to maintain sequential keys.
     * 
     * @param int $index The index of the item to remove (0-indexed)
     * @throws \OutOfBoundsException If the index doesn't exist
     */
    public function remove( int $index ): void {
        if ( !array_key_exists( $index, $this->array ) ) {
            throw new \OutOfBoundsException( "Index {$index} does not exist in the collection" );
        }
        
        unset( $this->array[$index] );
        $this->array = array_values( $this->array );
    }

    /**
     * Check if an index exists in the collection.
     * 
     * @param int $index The index to check (0-indexed)
     * @return bool True if the index exists, false otherwise
     */
    public function has( int $index ): bool {
        return array_key_exists( $index, $this->array );
    }

    /**
     * Get an item at a specific index.
     * 
     * @param int $index The index of the item to retrieve (0-indexed)
     * @return mixed The item at the specified index
     * @throws \OutOfBoundsException If the index doesn't exist
     */
    public function get( int $index ): mixed {
        if ( !array_key_exists( $index, $this->array ) ) {
            throw new \OutOfBoundsException( "Index {$index} does not exist in the collection" );
        }
        
        return $this->array[$index];
    }

    /**
     * Clear all items from the collection.
     */
    public function clear(): void {
        $this->array = [];
        $this->position = 0;
    }

    /**
     * Get all items as a plain array.
     * 
     * @return array The internal array
     */
    public function toArray(): array {
        return $this->array;
    }
}
