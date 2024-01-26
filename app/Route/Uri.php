<?php
namespace AdasFinance\Route;

class Uri
{
    public static function get(string $type): string
    {
        return parse_url($_SERVER['REQUEST_URI'])[$type];
    }
}