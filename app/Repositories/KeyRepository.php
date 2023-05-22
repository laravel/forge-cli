<?php

namespace App\Repositories;

use Illuminate\Support\Facades\File;
use Phar;
use Symfony\Component\Finder\Finder;

class KeyRepository
{
    /**
     * The keys path.
     *
     * @var string
     */
    protected $keysPath;

    /**
     * Creates a new repository instance.
     *
     * @param  string  $keysPath
     */
    public function __construct($keysPath)
    {
        $this->keysPath = $keysPath;
    }

    /**
     * Creates an SSH Key.
     *
     * @param  string  $name
     * @return array
     */
    public function create($name)
    {
        abort_if($this->exists($name), 1, 'The name has already been taken.');

        $basePath = empty($phar = Phar::running(false))
            ? base_path()
            : dirname($phar, 2);

        $keys = json_decode(exec($basePath.'/scripts/keysFactory.php'), true);

        File::put($this->keysPath.'/'.$this->privateKeyName($name), $keys['private']);
        File::chmod($this->keysPath.'/'.$this->privateKeyName($name), 0600);

        File::put($this->keysPath.'/'.($localName = $this->publicKeyName($name)), $keys['public']);

        return [$localName, $keys['public']];
    }

    /**
     * Checks if an SSH Key already exist.
     *
     * @param  string  $name
     * @return bool
     */
    public function exists($name)
    {
        return File::exists($this->keysPath.'/'.$this->privateKeyName($name))
            || File::exists($this->keysPath.'/'.$this->publicKeyName($name));
    }

    /**
     * Returns all local public SSH Keys.
     *
     * @return array
     */
    public function local()
    {
        return collect(Finder::create()->files()->in($this->keysPath)->depth(0)->name('*.pub'))
            ->keys()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Gets an SSH Key.
     *
     * @param  string  $key
     * @return array
     */
    public function get($key)
    {
        abort_unless(File::exists($key), 1, 'The given SSH Key does not exists.');

        return [
            basename($key),
            File::get($key),
        ];
    }

    /**
     * Gets the key path.
     *
     * @return string
     */
    public function keysPath()
    {
        return $this->keysPath;
    }

    /**
     * Gets the key name.
     *
     * @param  string  $name
     * @return string
     */
    protected function keyName($name)
    {
        return str_replace('-', '_', $name);
    }

    /**
     * Gets the private key name.
     *
     * @param  string  $name
     * @return string
     */
    protected function privateKeyName($name)
    {
        return $this->keyName($name);
    }

    /**
     * Gets the private key name.
     *
     * @param  string  $name
     * @return string
     */
    protected function publicKeyName($name)
    {
        return $this->privateKeyName($name).'.pub';
    }
}
