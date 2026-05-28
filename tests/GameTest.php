<?php

namespace Chess\Tests;

use Chess\Enum\PieceColor;
use Chess\Exception\InvalidMoveException;
use Chess\Exception\WrongTurnException;
use Chess\Game;
use Chess\Move;
use Chess\Piece\King;
use Chess\Piece\Rook;
use Chess\Position;
use PHPUnit\Framework\TestCase;

// Tests d orchestration pour Game.
class GameTest extends TestCase
{
    public function testStartSetsCurrentPlayerAndKings(): void
    {
        $game = new Game();
        $game->start();

        $this->assertSame(PieceColor::WHITE, $game->getCurrentPlayer());
        $this->assertNotNull($game->getBoard()->getKingPosition(PieceColor::WHITE));
        $this->assertNotNull($game->getBoard()->getKingPosition(PieceColor::BLACK));
    }

    public function testLegalMoveSwitchesPlayer(): void
    {
        $game = new Game();
        $game->start();

        $game->play(new Move(new Position(6, 4), new Position(4, 4)));

        $this->assertSame(PieceColor::BLACK, $game->getCurrentPlayer());
    }

    public function testWrongTurnThrows(): void
    {
        $game = new Game();
        $game->start();

        $this->expectException(WrongTurnException::class);
        $game->play(new Move(new Position(1, 4), new Position(3, 4)));
    }

    public function testIllegalMoveThrows(): void
    {
        $game = new Game();
        $game->start();

        $this->expectException(InvalidMoveException::class);
        $game->play(new Move(new Position(6, 4), new Position(0, 4)));
    }

    public function testCheckDetection(): void
    {
        $game = new Game();
        $board = $game->getBoard();
        $board->placePiece(new King(PieceColor::WHITE, new Position(7, 4)));
        $board->placePiece(new King(PieceColor::BLACK, new Position(0, 4)));
        $board->placePiece(new Rook(PieceColor::BLACK, new Position(7, 0)));

        $this->assertTrue($game->isCheck(PieceColor::WHITE));
    }
}
