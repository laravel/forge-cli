<?php

namespace App\Support;

class PhpVersion
{
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
     * @return void
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Creates a new PHP Version instance. Acts as static factory.
     *
     * @param  string $version
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
        $version = substr($this->version, -2);

        return number_format(substr($version, -2) / 10, 1, '.', '');
    }
}
