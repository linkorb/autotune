<?php

namespace AutoTune;

use RuntimeException;

class Tuner
{
    
    public static function init($loader, $basePath = null)
    {
        if (!$basePath) {
            // Assuming this is file is in vendor/linkorb/autotune/src/Tuner.php
            $basePath = __DIR__ . '/../../../../';
        }
        $tuner = new \AutoTune\Tuner();
        $tuner->setBasePath($basePath);
        $tuner->tune($loader);
        return $tuner;
    }
    
    private $basePath = null;
    
    private function getConfigFilename()
    {
        // Look for config in your project basepath
        if ($this->basePath) {
            if (file_exists($this->basePath . '/autotune.json')) {
                return $this->basePath . '/autotune.json';
            }
        }

        // Look for config in your home directory
        if (file_exists('~/autotune.json')) {
            return '~/autotune.json';
        }
        
        // Look for config in your system config directory
        if (file_exists('/etc/autotune.json')) {
            return '/etc/autotune.json';
        }
        
        // Nothing found
        return null;
    }
    
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        if (!file_exists($this->basePath)) {
            throw new RuntimeException("BasePath does not exist: " . $basePath);
        }
        if (!file_exists($basePath . '/composer.json')) {
            throw new RuntimeException("Invalid BasePath: " . $basePath . ' (expecting a composer.json file there)');
        }
    }
    private function getConfig()
    {
        $filename = $this->getConfigFilename();
        if (!$filename) {
            return false;
        }
        $json = file_get_contents($filename);
        
        $config = json_decode($json, true);
        
        return $config;
    }
    
    private function fixPath($path)
    {
        if ($path[0]=='~') {
            $path = $_SERVER['HOME'] . ltrim($path, '~');
        }
        
        if (!file_exists($path)) {
            throw new RuntimeException("Path not found: " . $path);
        }
        
        return $path;
    }
    
    public function tune($loader, $basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
        
        $config = $this->getConfig();
        
        if (!$config['autoload']) {
            throw new RuntimeException("No 'autoload' rules defined in autotune.json");
        }

        if (isset($config['autoload']['psr-0'])) {
            foreach ($config['autoload']['psr-0'] as $ns => $path) {
                $path = $this->fixPath($path);
                $loader->add($ns, $path, true);
            }
        }
        
        if (isset($config['autoload']['psr-4'])) {
            foreach ($config['autoload']['psr-4'] as $ns => $path) {
                $path = $this->fixPath($path);
                $loader->addPsr4($ns, $path, true);
            }
        }
        
        return true;
    }
}
