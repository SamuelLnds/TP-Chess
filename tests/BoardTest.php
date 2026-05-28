<?php

namespace Chess\Tests;

use Chess\Board;
use Chess\Enum\PieceColor;
use Chess\Piece\Pawn;
use Chess\Position;
use PHPUnit\Framework\TestCase;

// Tests de base pour Board.
class BoardTest extends TestCase
{
    public function testPlaceAndGetPiece(): void
    {
        $board = new Board();
        $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));

        $board->placePiece($pawn);

        $this->assertSame($pawn, $board->getPieceAt(new Position(6, 4)));
        $this->assertTrue($board->hasPieceAt(new Position(6, 4)));
    }

    public function testEmptySquare(): void
    {
        $board = new Board();

        $this->assertNull($board->getPieceAt(new Position(0, 0)));
        $this->assertFalse($board->hasPieceAt(new Position(0, 0)));
    }

    public function testDetectOccupationsByColor(): void
    {
        $board = new Board();
        $ally = new Pawn(PieceColor::WHITE, new Position(6, 4));
        $enemy = new Pawn(PieceColor::BLACK, new Position(5, 5));

        $board->placePiece($ally);
        $board->placePiece($enemy);

        $this->assertSame(PieceColor::WHITE, $board->getPieceAt(new Position(6, 4))->getColor());
        $this->assertSame(PieceColor::BLACK, $board->getPieceAt(new Position(5, 5))->getColor());
    }
}
