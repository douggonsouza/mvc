<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80e6afd09195ba30855030970ddea808
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Douggonsouza\\Mvc\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Douggonsouza\\Mvc\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit80e6afd09195ba30855030970ddea808::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80e6afd09195ba30855030970ddea808::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit80e6afd09195ba30855030970ddea808::$classMap;

        }, null, ClassLoader::class);
    }
}
