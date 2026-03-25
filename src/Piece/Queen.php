<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class Queen extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::QUEEN);
    }

    #endregion

    #region Overrides

    public function isValidMovementShape(Position $target): bool
    {
        // Récupération des informations nécessaires à la vérif
        $rowDiff = $target->getRow() - $this->getPosition()->getRow();
        $colDiff = $target->getColumn() - $this->getPosition()->getColumn();

        // Reine en ligne ou en diagonale
        if (
            ($rowDiff === 0 && $colDiff !== 0) ||
            ($rowDiff !== 0 && $colDiff === 0) ||
            ($rowDiff !== 0 && $colDiff !== 0 && $rowDiff === $colDiff)
        ) {
            return true;
        }

        return false;
    }

    #endregion
}
