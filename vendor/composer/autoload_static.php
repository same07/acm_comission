<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit860c146b8d55fcbf1d865d6fe90ad72b
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Memiles\\Comission\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Memiles\\Comission\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit860c146b8d55fcbf1d865d6fe90ad72b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit860c146b8d55fcbf1d865d6fe90ad72b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit860c146b8d55fcbf1d865d6fe90ad72b::$classMap;

        }, null, ClassLoader::class);
    }
}
