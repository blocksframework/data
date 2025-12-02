# Collection

Abstract base class for iterable collections with sequential integer keys.

## Overview

`Collection` provides a foundation for building custom collection types that implement PHP's `Iterator` and `Countable` interfaces. It handles the low-level iteration mechanics while allowing subclasses to define how items are retrieved and processed.

Key features:
- **Sequential integer keys**: Arrays are automatically normalized to 0-indexed sequential keys
- **Iterator support**: Use collections in `foreach` loops
- **Countable**: Get collection size with `count()`
- **Null-safe iteration**: Properly handles `null` values in collections
- **Mutation methods**: Add, remove, and clear items
- **Bounds checking**: Safe indexed access with exception handling

## Namespace

```php
Blocks\Data\Collection
```

## Basic Usage

### Creating a Collection

Since `Collection` is abstract, you must create a concrete subclass:

```php
use Blocks\Data\Collection;

class StringCollection extends Collection {
    public function current(): mixed {
        if ( !$this->valid() ) {
            throw new \OutOfBoundsException( 'Iterator position is invalid' );
        }
        return $this->array[$this->position];
    }
}

// Create with initial items
$collection = new StringCollection( ['apple', 'banana', 'cherry'] );
```

### Iterating

```php
foreach ( $collection as $index => $item ) {
    echo "{$index}: {$item}\n";
}
// Output:
// 0: apple
// 1: banana
// 2: cherry
```

### Counting

```php
$count = count( $collection );
echo "Collection has {$count} items";
```

## API Reference

### Constructor

```php
public function __construct( array $items )
```

Creates a new collection from an array of items. Array keys are automatically reindexed to sequential integers (0, 1, 2...).

**Parameters:**
- `$items` (array) - Initial items to populate the collection

**Example:**
```php
$collection = new MyCollection( ['a' => 'apple', 'b' => 'banana'] );
// Keys are normalized: [0 => 'apple', 1 => 'banana']
```

---

### current()

```php
abstract public function current(): mixed
```

Returns the current element during iteration. **Subclasses must implement this method.**

**Returns:** The current element

**Recommended implementation:**
```php
public function current(): mixed {
    if ( !$this->valid() ) {
        throw new \OutOfBoundsException( 'Iterator position is invalid' );
    }
    return $this->array[$this->position];
}
```

---

### add()

```php
public function add( mixed $item ): void
```

Adds an item to the end of the collection.

**Parameters:**
- `$item` (mixed) - The item to add

**Example:**
```php
$collection->add( 'orange' );
```

---

### remove()

```php
public function remove( int $index ): void
```

Removes an item at a specific index. After removal, the array is reindexed to maintain sequential keys.

**Parameters:**
- `$index` (int) - The index of the item to remove (0-indexed)

**Throws:** `\OutOfBoundsException` if the index doesn't exist

**Example:**
```php
$collection->remove( 1 ); // Removes item at index 1

// Before removing, check if it exists
if ( $collection->has( $index ) ) {
    $collection->remove( $index );
}

// Or just try and catch the exception
try {
    $collection->remove( $index );
} catch ( \OutOfBoundsException $e ) {
    // Handle missing index
}
```

---

### has()

```php
public function has( int $index ): bool
```

Checks if an index exists in the collection.

**Parameters:**
- `$index` (int) - The index to check (0-indexed)

**Returns:** `true` if the index exists, `false` otherwise

**Example:**
```php
if ( $collection->has( 2 ) ) {
    echo "Item at index 2 exists";
}
```

---

### get()

```php
public function get( int $index ): mixed
```

Gets an item at a specific index with bounds checking.

**Parameters:**
- `$index` (int) - The index of the item to retrieve (0-indexed)

**Returns:** The item at the specified index

**Throws:** `\OutOfBoundsException` if the index doesn't exist

**Example:**
```php
try {
    $item = $collection->get( 0 );
    echo $item;
} catch ( \OutOfBoundsException $e ) {
    echo "Index not found";
}
```

---

### clear()

```php
public function clear(): void
```

Removes all items from the collection and resets the iterator position.

**Example:**
```php
$collection->clear();
echo count( $collection ); // 0
```

---

### toArray()

```php
public function toArray(): array
```

Returns all items as a plain PHP array with sequential integer keys.

**Returns:** The internal array

**Example:**
```php
$array = $collection->toArray();
print_r( $array );
```

---

### count()

```php
public function count(): int
```

Returns the number of items in the collection. Implements the `Countable` interface.

**Returns:** The number of items

**Example:**
```php
$size = count( $collection );
// or
$size = $collection->count();
```

## Iterator Methods

These methods implement the `Iterator` interface and are used internally by `foreach` loops. You typically don't call them directly.

### rewind()

```php
public function rewind(): void
```

Resets the iterator to the first element.

---

### key()

```php
public function key(): int
```

Returns the key (index) of the current element.

**Returns:** The current position (0-indexed)

---

### next()

```php
public function next(): void
```

Moves the iterator forward to the next element.

---

### valid()

```php
public function valid(): bool
```

Checks if the current iterator position is valid. Uses `array_key_exists()` to properly handle `null` values in the collection.

**Returns:** `true` if the current position exists in the array

## Advanced Examples

### Custom Typed Collection

```php
class UserCollection extends Collection {
    public function current(): User {
        if ( !$this->valid() ) {
            throw new \OutOfBoundsException( 'Iterator position is invalid' );
        }
        return $this->array[$this->position];
    }
    
    public function add( User $user ): void {
        parent::add( $user );
    }
    
    public function findByEmail( string $email ): ?User {
        foreach ( $this as $user ) {
            if ( $user->getEmail() === $email ) {
                return $user;
            }
        }
        return null;
    }
}
```

### Handling Null Values

```php
$collection = new MyCollection( [1, 2, null, 4] );

foreach ( $collection as $item ) {
    if ( $item === null ) {
        echo "Found null value\n";
    }
}
// Collection properly handles null items during iteration
```

### Safe Manipulation

```php
$collection = new StringCollection( ['a', 'b', 'c'] );

// Add items
$collection->add( 'd' );
$collection->add( 'e' );

// Check before accessing
if ( $collection->has( 3 ) ) {
    $item = $collection->get( 3 );
    echo "Item 3: {$item}\n";
}

// Remove item (with reindexing)
$collection->remove( 1 ); // Removes 'b'
// Collection is now: [0 => 'a', 1 => 'c', 2 => 'd', 3 => 'e']

// Clear all
$collection->clear();
echo "Size: " . count( $collection ); // Size: 0
```

## Implementation Notes

### Key Normalization

Collections automatically normalize array keys to sequential integers starting at 0. This ensures consistent iteration behavior regardless of the input array structure:

```php
$input = ['x' => 'apple', 'y' => 'banana', 'z' => 'cherry'];
$collection = new MyCollection( $input );

// Internal array is: [0 => 'apple', 1 => 'banana', 2 => 'cherry']
```

### Reindexing on Removal

When you remove an item, the collection automatically reindexes to maintain sequential keys:

```php
$collection = new MyCollection( ['a', 'b', 'c', 'd'] );
$collection->remove( 1 ); // Remove 'b'

// Before: [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd']
// After:  [0 => 'a', 1 => 'c', 2 => 'd']
```

### Exception Handling

Methods that access items by index throw `\OutOfBoundsException` for invalid indexes:

- `remove( $index )`
- `get( $index )`
- `current()` (recommended in subclass implementation)

Always use `has()` to check if an index exists before accessing, or wrap calls in try-catch blocks.

## See Also

- [PHP Iterator Interface](https://www.php.net/manual/en/class.iterator.php)
- [PHP Countable Interface](https://www.php.net/manual/en/class.countable.php)
