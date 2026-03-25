<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class Bishop extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::BISHOP);
    }

    #endregion

    #region Overrides

    public function isValidMovementShape(Position $target): bool
    {
        // Récupération des informations nécessaires à la vérif
        $rowDiff = $target->getRow() - $this->getPosition()->getRow();
        $colDiff = $target->getColumn() - $this->getPosition()->getColumn();

        // Fou en diagonale
        if ($rowDiff !== 0 && $colDiff !== 0 && $rowDiff === $colDiff) {
            return true;
        }

        return false;
    }

    #endregion

}
