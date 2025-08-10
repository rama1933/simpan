<?php

namespace App\Data;

abstract class BaseDTO
{
    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Convert DTO to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get only specified fields
     */
    public function only(array $fields): array
    {
        return array_intersect_key($this->toArray(), array_flip($fields));
    }

    /**
     * Get all fields except specified ones
     */
    public function except(array $fields): array
    {
        return array_diff_key($this->toArray(), array_flip($fields));
    }

    /**
     * Check if field exists
     */
    public function has(string $field): bool
    {
        return property_exists($this, $field);
    }

    /**
     * Get field value
     */
    public function get(string $field, $default = null)
    {
        return $this->has($field) ? $this->{$field} : $default;
    }

    /**
     * Set field value
     */
    public function set(string $field, $value): static
    {
        if ($this->has($field)) {
            $this->{$field} = $value;
        }
        return $this;
    }
}

