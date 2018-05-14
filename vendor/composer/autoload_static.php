<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd4bbe513f351000493153baa462a6503
{
    public static $files = array (
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Http\\Message\\' => 17,
            'PhpOffice\\PhpSpreadsheet\\' => 25,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'F' => 
        array (
            'FlyingLuscas\\ViaCEP\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'PhpOffice\\PhpSpreadsheet\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpspreadsheet/src/PhpSpreadsheet',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'FlyingLuscas\\ViaCEP\\' => 
        array (
            0 => __DIR__ . '/..' . '/flyingluscas/viacep-php/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'F' => 
        array (
            'ForceUTF8\\' => 
            array (
                0 => __DIR__ . '/..' . '/neitanod/forceutf8/src',
            ),
        ),
    );

    public static $classMap = array (
        'DAO' => __DIR__ . '/..' . '/phpclasses/dao-for-php/DAO.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd4bbe513f351000493153baa462a6503::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd4bbe513f351000493153baa462a6503::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitd4bbe513f351000493153baa462a6503::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitd4bbe513f351000493153baa462a6503::$classMap;

        }, null, ClassLoader::class);
    }
}
