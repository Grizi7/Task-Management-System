<?php

if (! function_exists('getFilesFromPath')) {
    /**
     * Get all PHP file basenames from a given directory path.
     *
     * @param string $path
     * @return \Illuminate\Support\Collection
     */
    function getFilesFromPath($path)
    {
        if (! is_dir($path)) {
            return collect(); // or throw an exception
        }

        return collect(iterator_to_array(
            new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path))
        ))->filter(function ($file) {
            return $file->isFile() && $file->getExtension() === 'php';
        })->map(function ($file) {
            return $file->getBasename('.php');
        })->values();
    }
}