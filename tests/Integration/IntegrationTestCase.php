<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Base integration test case for common behaviour across the integration tests of Kunena: a test which implements
 * DBTestInterface gets its database (or is skipped when there is none).
 */
abstract class IntegrationTestCase extends TestCase
{
    /**
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ($this instanceof DBTestInterface) {
            try {
                DBTestHelper::setupTest($this);
            } catch (DBUnavailableException $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }
    }
}
