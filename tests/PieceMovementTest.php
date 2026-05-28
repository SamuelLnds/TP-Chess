<?php

namespace Chess\Tests;

use Chess\Board;
use Chess\Enum\PieceColor;
use Chess\Exception\InvalidMoveException;
use Chess\Exception\OccupiedByAllyException;
use Chess\Piece\Bishop;
use Chess\Piece\King;
use Chess\Piece\Knight;
use Chess\Piece\Pawn;
use Chess\Piece\Queen;
use Chess\Piece\Rook;
use Chess\Position;
use PHPUnit\Framework\TestCase;

// Tests de deplacement des pieces sur un plateau.
class PieceMovementTest extends TestCase
{
    // Pion
    public function testPawnValidMovesOnEmptyBoard(): void
    {
        $board = new Board();
        $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));
        $board->placePiece($pawn);

        $this->assertTrue($pawn->canMove($board, new Position(5, 4)));
        $this->assertTrue($pawn->canMove($board, new Position(4, 4)));
    }

    public function testPawnCannotMoveDiagonallyWithoutCapture(): void
    {
        $board = new Board();
        $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));
        $board->placePiece($pawn);

        $this->expectException(InvalidMoveException::class);
        $pawn->canMove($board, new Position(5, 5));
    }

    public function testPawnCannotMoveForwardIntoOccupiedSquare(): void
    {
        $board = new Board();
        $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));
        $blocker = new Pawn(PieceColor::BLACK, new Position(5, 4));
        $board->placePiece($pawn);
        $board->placePiece($blocker);

        $this->expectException(InvalidMoveException::class);
        $pawn->canMove($board, new Position(5, 4));
    }

    public function testPawnCanCaptureDiagonally(): void
    {
        $board = new Board();
        $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));
        $enemy = new Pawn(PieceColor::BLACK, new Position(5, 5));
        $board->placePiece($pawn);
        $board->placePiece($enemy);

        $this->assertTrue($pawn->canMove($board, new Position(5, 5)));
    }

    // Cavalier
    public function testKnightValidLMove(): void
    {
        $board = new Board();
        $knight = new Knight(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($knight);

        $this->assertTrue($knight->canMove($board, new Position(2, 5)));
    }

    public function testKnightInvalidMove(): void
    {
        $board = new Board();
        $knight = new Knight(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($knight);

        $this->expectException(InvalidMoveException::class);
        $knight->canMove($board, new Position(5, 5));
    }

    public function testKnightCannotCaptureAlly(): void
    {
        $board = new Board();
        $knight = new Knight(PieceColor::WHITE, new Position(4, 4));
        $ally = new Pawn(PieceColor::WHITE, new Position(2, 5));
        $board->placePiece($knight);
        $board->placePiece($ally);

        $this->expectException(OccupiedByAllyException::class);
        $knight->canMove($board, new Position(2, 5));
    }

    // Fou
    public function testBishopMovesDiagonally(): void
    {
        $board = new Board();
        $bishop = new Bishop(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($bishop);

        $this->assertTrue($bishop->canMove($board, new Position(7, 7)));
    }

    public function testBishopCannotJumpOverPiece(): void
    {
        $board = new Board();
        $bishop = new Bishop(PieceColor::WHITE, new Position(4, 4));
        $blocker = new Pawn(PieceColor::WHITE, new Position(5, 5));
        $board->placePiece($bishop);
        $board->placePiece($blocker);

        $this->expectException(InvalidMoveException::class);
        $bishop->canMove($board, new Position(7, 7));
    }

    // Tour
    public function testRookMovesStraight(): void
    {
        $board = new Board();
        $rook = new Rook(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($rook);

        $this->assertTrue($rook->canMove($board, new Position(4, 7)));
    }

    public function testRookCannotJumpOverPiece(): void
    {
        $board = new Board();
        $rook = new Rook(PieceColor::WHITE, new Position(4, 4));
        $blocker = new Pawn(PieceColor::WHITE, new Position(4, 6));
        $board->placePiece($rook);
        $board->placePiece($blocker);

        $this->expectException(InvalidMoveException::class);
        $rook->canMove($board, new Position(4, 7));
    }

    // Reine
    public function testQueenMovesDiagonallyOrStraight(): void
    {
        $board = new Board();
        $queen = new Queen(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($queen);

        $this->assertTrue($queen->canMove($board, new Position(7, 7)));
        $this->assertTrue($queen->canMove($board, new Position(4, 0)));
    }

    public function testQueenCannotJumpOverPiece(): void
    {
        $board = new Board();
        $queen = new Queen(PieceColor::WHITE, new Position(4, 4));
        $blocker = new Pawn(PieceColor::WHITE, new Position(4, 2));
        $board->placePiece($queen);
        $board->placePiece($blocker);

        $this->expectException(InvalidMoveException::class);
        $queen->canMove($board, new Position(4, 0));
    }

    // Roi
    public function testKingMovesOneSquare(): void
    {
        $board = new Board();
        $king = new King(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($king);

        $this->assertTrue($king->canMove($board, new Position(5, 5)));
    }

    public function testKingInvalidMove(): void
    {
        $board = new Board();
        $king = new King(PieceColor::WHITE, new Position(4, 4));
        $board->placePiece($king);

        $this->expectException(InvalidMoveException::class);
        $king->canMove($board, new Position(6, 4));
    }
}
