<?php

namespace Chess\Tests;

use Chess\Position;
use PHPUnit\Framework\TestCase;

// Tests de valeur pour Position.
class PositionTest extends TestCase
{
    public function testEqualsSameCoordinates(): void
    {
        $a = new Position(6, 4);
        $b = new Position(6, 4);

        $this->assertTrue($a->equals($b));
    }

    public function testNotEqualsDifferentCoordinates(): void
    {
        $a = new Position(6, 4);
        $b = new Position(5, 4);

        $this->assertFalse($a->equals($b));
    }

    public function testToKeyFromKeyRoundTrip(): void
    {
        $position = new Position(3, 2);
        $key = $position->toKey();
        $roundTrip = Position::fromKey($key);

        $this->assertTrue($position->equals($roundTrip));
        $this->assertSame($key, $roundTrip->toKey());
    }
}
