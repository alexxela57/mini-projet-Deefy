<?php

namespace iutnc\deefy\loader;
class autoLoader {
    private string $prefix;
    private string $baseDir;

    public function __construct(string $prefix, string $baseDir) {
        $this->prefix = rtrim($prefix,'\\').'\\';
        $this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . 'autoLoader.php/';
    }
    public function loadClass(string $className):void{
        if (strpos($className, $this->prefix) !== 0) {
            return;
        }
        $relativeClass = str_replace($this->prefix, '', $className);
        $filePath = $this->baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        // Inclut le fichier si il existe
        if (is_file($filePath)) {
            require_once $filePath;
        }
    }

    public function register(): void {
        spl_autoload_register([$this, 'loadClass']);
    }
}