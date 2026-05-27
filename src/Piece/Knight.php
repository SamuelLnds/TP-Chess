<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class Knight extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::KNIGHT);
    }

    #endregion

    #region Overrides

    public function isValidMovementShape(Position $target): bool
    {
        // Récupération des informations nécessaires à la vérif
        $rowDiff = $target->getRow() - $this->getPosition()->getRow();
        $colDiff = $target->getColumn() - $this->getPosition()->getColumn();

        // Cavalier en L
        if ((abs($rowDiff) === 2 && abs($colDiff) === 1) || (abs($rowDiff) === 1 && abs($colDiff) === 2)) {
            return true;
        }

        return false;
    }

    #endregion
}
