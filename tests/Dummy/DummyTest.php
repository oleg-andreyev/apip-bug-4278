<?php

/*
 * This file is part of the Ecentria group, inc. software.
 *
 * (c) 2020, Ecentria group, inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Dummy;

use PHPUnit\Framework\TestCase;

/**
 * Dummy test
 *
 * @author Sergey Chernecov <sergey.chernecov@ecentria.com>
 */
class DummyTest extends TestCase
{
    /**
     * Test dummy
     *
     * @return void
     */
    public function testValidate(): void
    {
        $this->assertEquals(200, 200);
    }
}
