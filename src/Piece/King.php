<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class King extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::KING);
    }

    #endregion

    #region Overrides

    protected function isValidMovementShape(Position $target): bool
    {
        $rowDiff = abs($target->getRow() - $this->getPosition()->getRow());
        $colDiff = abs($target->getColumn() - $this->getPosition()->getColumn());

        // Roi en 1 case dans n'importe quelle direction
        if ($rowDiff <= 1 && $colDiff <= 1) {
            return true;
        }

        // Roque — conditions supplémentaires vérifiées dans Game
        if ($rowDiff === 0 && $colDiff === 2 && !$this->hasMoved()) {
            return true;
        }

        return false;
    }

    #endregion
}
