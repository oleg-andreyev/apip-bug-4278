<?php

/*
 * This file is part of the ecentria software.
 *
 * (c) 2018, ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

/**
 * Application
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
final class Application
{
    const KEY_REPOSITORY = 'repository';
    const KEY_SOURCE_CACHE_PATH = 'source_cache_path';
    const KEY_RELEASE_PATH = 'release_path';
    const KEY_WRITABLE_DIRS = 'writable_dirs';
    const KEY_DEPLOY_PATH = 'deploy_path';
    const KEY_EXECUTE_MIGRATIONS = 'execute_migrations';
    const KEY_BRANCH = 'branch';

    /**
     * Build
     *
     * @return void
     */
    public static function build()
    {
        self::buildInventory();
        self::registerTasks();
    }

    /**
     * Load variables
     *
     * @return void
     */
    public static function loadVariables()
    {
        set(self::KEY_SOURCE_CACHE_PATH, '{{' . self::KEY_DEPLOY_PATH . '}}/cached-copy');
    }

    /**
     * Build inventory
     *
     * @return void
     */
    private static function buildInventory()
    {
        inventory(__DIR__ . '/Inventory/hosts.yml');
    }

    /**
     * Register shared tasks
     *
     * @return void
     */
    private static function registerTasks()
    {
        /**
         * Custom made
         */
        task('custom:setup', TaskBuilder::buildSetupCallback())->setPrivate();
        task('custom:cached_copy_update', TaskBuilder::buildCachedCopyUpdateCallback())->setPrivate();
        task('custom:clear_opcache', TaskBuilder::buildClearOpCacheCallback())->setPrivate();
        task('custom:phpfpm:reload', TaskBuilder::buildPhpFpmRestartCallback())->setPrivate();
        task('custom:migrations', TaskBuilder::buildDatabaseMigrationCallback())->once()->setPrivate();

        /**
         * Overrides
         */
        task('deploy:update_code', TaskBuilder::buildUpdateCodeCallback())->setPrivate();

        /**
         * Temporary
         */
        after('custom:setup', 'deploy:unlock');

        /**
         * Sequence
         */
        before('deploy', 'custom:setup');

        after('deploy:failed', 'deploy:unlock');
        after('deploy:vendors', 'custom:cached_copy_update');
        after('deploy:vendors', 'custom:migrations');

        before('cleanup', 'custom:clear_opcache');
        before('cleanup', 'custom:phpfpm:reload');
    }
}
