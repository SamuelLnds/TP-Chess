<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class Pawn extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::PAWN);
    }

    #endregion

    #region Overrides

    public function isValidMovementShape(Position $target): bool
    {
        // Récupération des informations nécessaires à la vérif
        $direction = $this->getDirection();
        $rowDiff = $target->getRow() - $this->getPosition()->getRow();
        $colDiff = $target->getColumn() - $this->getPosition()->getColumn();
        $currentRow = $this->getPosition()->getRow();

        // Vérifier la direction, si impossible alors retour immédiat
        if ($rowDiff != $direction && $rowDiff != 2 * $direction) {
            return false;
        }

        $return = false;

        // Avance simple (1 case)
        if ($rowDiff === $direction && $colDiff === 0) {
            $return = true;
        }

        // Avance double au départ (2 cases)
        if ($rowDiff === 2 * $direction && $colDiff === 0 && $currentRow === 1) {
            $return = true;
        }

        // Capture en diagonale (1 case diagonale)
        if ($rowDiff === $direction && $colDiff === 1) {
            $return = true;
        }

        return $return;
    }

    #endregion
}
