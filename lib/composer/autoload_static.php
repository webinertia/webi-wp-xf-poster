<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbccd24f179b4ea48b546145692aa748f
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WebiXfBridge\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WebiXfBridge\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbccd24f179b4ea48b546145692aa748f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbccd24f179b4ea48b546145692aa748f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbccd24f179b4ea48b546145692aa748f::$classMap;

        }, null, ClassLoader::class);
    }
}