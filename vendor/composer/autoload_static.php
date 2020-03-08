<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb28d8e906e986c1ecd2201470869d029
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'servicetech\\' => 12,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'servicetech\\' => 
        array (
            0 => __DIR__ . '/..' . '/servicetech/core',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb28d8e906e986c1ecd2201470869d029::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb28d8e906e986c1ecd2201470869d029::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
