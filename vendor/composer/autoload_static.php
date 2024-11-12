<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita79f4cc9b5862b401a4d26cb30ad1023
{
    public static $files = array (
        '3749e76c9d94066b08f1cacd86f925ce' => __DIR__ . '/../..' . '/app/routes/helpers/helpersRoutes.php',
    );

    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'Libs\\' => 5,
        ),
        'D' => 
        array (
            'Db\\' => 3,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Libs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/libs',
        ),
        'Db\\' => 
        array (
            0 => __DIR__ . '/../..' . '/db',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita79f4cc9b5862b401a4d26cb30ad1023::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita79f4cc9b5862b401a4d26cb30ad1023::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita79f4cc9b5862b401a4d26cb30ad1023::$classMap;

        }, null, ClassLoader::class);
    }
}