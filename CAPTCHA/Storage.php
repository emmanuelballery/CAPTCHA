<?php

namespace CAPTCHA;

/**
 * Class Storage
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Storage
{
    /**
     * @var array
     */
    private $storage;

    /**
     * @param array $storage
     */
    public function __construct(array &$storage)
    {
        $this->storage = & $storage;
    }

    /**
     * Set a value
     *
     * @param mixed $key   Key
     * @param mixed $value Value
     *
     * @return Storage
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;

        return $this;
    }

    /**
     * Get a value
     *
     * @param mixed $key Key
     *
     * @return null|mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }

        return null;
    }
}
