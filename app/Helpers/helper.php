<?php

if (!function_exists('modules_path')) {
    function modules_path(string $path = ''): string
    {
        return base_path('src/Modules/' . $path);
    }
}
