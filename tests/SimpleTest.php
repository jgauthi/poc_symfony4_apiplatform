<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testAdditional()
    {
        $this->assertEquals(5, 2 + 3);
    }
}
