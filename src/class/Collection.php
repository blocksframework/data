<?php

namespace Blocks\Data;

use Iterator;
use Countable;

abstract class Collection implements Iterator, Countable {
    protected $position = 0;

    protected array $array;

    public function __construct(array $items) {
        $this->array = [];

        if ( !empty($items) ) {
            if ( is_array($items) ) {
                $this->array = $items;
            } else {
                $this->array[] = $items;
            }
        }
    }

    public function rewind(): void {
        $this->position = 0;
    }

    abstract public function current();

    #[\ReturnTypeWillChange]
    public function key() {
        return $this->position;
    }

    public function next(): void {
        ++$this->position;
    }

    public function valid(): bool {
        return isset($this->array[ $this->position ]);
    }

    public function count(): int {
        return count($this->array);
    }
}
