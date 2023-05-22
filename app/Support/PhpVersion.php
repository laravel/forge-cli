<?php

namespace App\Support;

class PhpVersion
{
    /**
     * The available PHP versions.
     *
     * @var array<int, string>
     */
    const VERSIONS = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4', '8.0', '8.1', '8.2'];

    /**
     * The PHP version.
     *
     * @var string
     */
    protected $version;

    /**
     * Creates a new PHP Version instance.
     *
     * @param  string  $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Creates a new PHP Version instance. Acts as static factory.
     *
     * @param  string  $version
     * @return static
     */
    public static function of($version)
    {
        return new static($version);
    }

    /**
     * Gets the binary.
     *
     * @return string
     */
    public function binary()
    {
        return 'php'.$this->release();
    }

    /**
     * Gets the release.
     *
     * @return string
     */
    public function release()
    {
        $version = (int) substr($this->version, -2);

        return number_format($version / 10, 1, '.', '');
    }
}
