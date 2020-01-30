<?php

declare (strict_types = 1);

namespace App;

use App\listing\inc\Misc;
use App\listing\Search;
use DI\ContainerBuilder;
use function DI\create;
use Psr\Container\ContainerInterface;

class Classes
{
    private static $containerBuilder = null;
    private static $container = null;

    public function __construct()
    {}

    public static function setContainerBuilder()
    {
        self::$containerBuilder = new ContainerBuilder();
        self::$containerBuilder->useAutowiring(true);
        self::$containerBuilder->useAnnotations(true);
    }

    public static function setContainer($container)
    {
        self::$container = $container;
    }

    /**
     * create container of all classes
     */
    public static function registerInContainer(): ContainerInterface
    {
        if (self::$container == null) {
            self::setContainerBuilder();

            self::$containerBuilder->addDefinitions([
                Misc::class => create(Misc::class)
                    ->constructor(),
                Search::class => create(Search::class)
                    ->constructor(),
            ]);

            /** @noinspection PhpUnhandledExceptionInspection */
            self::setContainer(self::$containerBuilder->build());
        }

        return self::$container;
    }
}
