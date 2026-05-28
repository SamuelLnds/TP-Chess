<?php

namespace Chess\Tests;

use Chess\Move;
use Chess\Position;
use PHPUnit\Framework\TestCase;

// Tests de valeur pour Move.
class MoveTest extends TestCase
{
    public function testEqualsSameFromTo(): void
    {
        $moveA = new Move(new Position(6, 4), new Position(5, 4));
        $moveB = new Move(new Position(6, 4), new Position(5, 4));

        $this->assertTrue($moveA->getFrom()->equals($moveB->getFrom()));
        $this->assertTrue($moveA->getTo()->equals($moveB->getTo()));
    }

    public function testNotEqualsDifferentFromOrTo(): void
    {
        $moveA = new Move(new Position(6, 4), new Position(5, 4));
        $moveB = new Move(new Position(6, 3), new Position(5, 4));
        $moveC = new Move(new Position(6, 4), new Position(5, 5));

        $this->assertFalse($moveA->getFrom()->equals($moveB->getFrom()));
        $this->assertFalse($moveA->getTo()->equals($moveC->getTo()));
    }

    public function testPositionsAreStoredAsGiven(): void
    {
        $from = new Position(6, 4);
        $to = new Position(5, 4);
        $move = new Move($from, $to);

        $this->assertSame($from, $move->getFrom());
        $this->assertSame($to, $move->getTo());
    }
}
