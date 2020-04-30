<?php

/*
 * This file is part of the Ecentria group, inc. software.
 *
 * (c) 2020, Ecentria group, inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class            => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class                      => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class              => ['all' => true],
    Nelmio\CorsBundle\NelmioCorsBundle::class                        => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class             => ['all' => true],
    ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class  => ['all' => true],
    Ecentria\LoggingBundle\EcentriaLoggingBundle::class              => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class                => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class                    => ['dev' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
];