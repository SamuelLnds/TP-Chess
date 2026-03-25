<?php

namespace Chess;

use Chess\Contract\Renderable;
use Chess\Enum\PieceColor;
use Chess\Piece\Piece;
use LogicException;

class Board implements Renderable
{
    public function getPieceAt(Position $position): ?Piece
    {
        throw new LogicException('La méthode Board::getPieceAt() n\'est pas encore implémentée.');
    }

    public function isAllyAt(Position $position, PieceColor $color): bool
    {
        throw new LogicException('La méthode Board::isAllyAt() n\'est pas encore implémentée.');
    }

    public function isPathClear(Position $from, Position $to): bool
    {
        throw new LogicException('La méthode Board::isPathClear() n\'est pas encore implémentée.');
    }

    public function render(): string
    {
        return '';
    }
}
