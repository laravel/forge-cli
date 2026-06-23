<?php

namespace App\Repositories;

class LocalConfigRepository
{
    /**
     * The config file name.
     */
    const CONFIG_FILE = '.forge';

    /**
     * The cached config data.
     *
     * @var array|null
     */
    protected $config = null;

    /**
     * The path where the config file was found.
     *
     * @var string|null
     */
    protected $foundPath = null;

    /**
     * Get all of the local configuration items.
     *
     * @return array
     */
    public function all()
    {
        if ($this->config !== null) {
            return $this->config;
        }

        $path = $this->findConfigFile();

        if ($path === null) {
            $this->config = [];
            return $this->config;
        }

        $this->foundPath = $path;
        $contents = file_get_contents($path);
        $this->config = json_decode($contents, true) ?: [];

        return $this->config;
    }

    /**
     * Get a local configuration value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $config = $this->all();

        return $config[$key] ?? $default;
    }

    /**
     * Check if the config uses named environments.
     *
     * @return bool
     */
    public function hasEnvironments()
    {
        return isset($this->all()['environments']);
    }

    /**
     * Get the list of available environment names.
     *
     * @return array
     */
    public function getEnvironmentNames()
    {
        $config = $this->all();

        if (!isset($config['environments'])) {
            return [];
        }

        return array_keys($config['environments']);
    }

    /**
     * Get the default environment name.
     *
     * @return string|null
     */
    public function getDefaultEnvironment()
    {
        return $this->get('default');
    }

    /**
     * Get an environment configuration by name.
     *
     * @param  string  $name
     * @return array|null
     */
    public function getEnvironment($name)
    {
        $config = $this->all();

        return $config['environments'][$name] ?? null;
    }

    /**
     * Resolve the environment to use.
     *
     * Priority:
     * 1. Explicitly specified environment name
     * 2. Default environment from config
     * 3. null if no environments configured
     *
     * @param  string|null  $specified
     * @return array|null  Returns ['name' => string, 'config' => array] or null
     */
    public function resolveEnvironment($specified = null)
    {
        $config = $this->all();

        // If no environments configured, return legacy format if present
        if (!$this->hasEnvironments()) {
            if (isset($config['server'])) {
                return [
                    'name' => null,
                    'config' => [
                        'server' => $config['server'],
                        'site' => $config['site'] ?? null,
                        'confirm' => $config['confirm'] ?? false,
                    ],
                ];
            }
            return null;
        }

        // Determine which environment to use
        $envName = $specified ?? $this->getDefaultEnvironment();

        if ($envName === null) {
            return null;
        }

        $envConfig = $this->getEnvironment($envName);

        if ($envConfig === null) {
            return null;
        }

        return [
            'name' => $envName,
            'config' => $envConfig,
        ];
    }

    /**
     * Check if a local config file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->findConfigFile() !== null;
    }

    /**
     * Get the path where the config file was found.
     *
     * @return string|null
     */
    public function getFoundPath()
    {
        $this->all(); // Ensure we've searched for the file

        return $this->foundPath;
    }

    /**
     * Find the config file by walking up from cwd.
     *
     * @return string|null
     */
    protected function findConfigFile()
    {
        $directory = getcwd();

        while (true) {
            $configPath = $directory . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

            if (file_exists($configPath)) {
                return $configPath;
            }

            $parentDirectory = dirname($directory);

            // We've hit the root
            if ($parentDirectory === $directory) {
                return null;
            }

            $directory = $parentDirectory;
        }
    }

    /**
     * Create a new config file in the given directory.
     *
     * @param  string  $directory
     * @param  array  $config
     * @return string The path to the created file
     */
    public function create($directory, array $config)
    {
        $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . self::CONFIG_FILE;

        file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

        // Reset cache so next read picks up new file
        $this->config = null;
        $this->foundPath = null;

        return $path;
    }
}
