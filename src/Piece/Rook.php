<?php

namespace Chess\Piece;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;

class Rook extends Piece
{
    #region Constructeur

    public function __construct(PieceColor $color, Position $position)
    {
        parent::__construct($color, $position, PieceType::ROOK);
    }

    #endregion

    #region Méthodes

     public function isValidMovementShape(Position $target): bool
     {
         // Récupération des informations nécessaires à la vérif
         $rowDiff = $target->getRow() - $this->getPosition()->getRow();
         $colDiff = $target->getColumn() - $this->getPosition()->getColumn();

         // Vérification de la validité du déplacement
         return ($rowDiff === 0 && $colDiff !== 0) || ($rowDiff !== 0 && $colDiff === 0);
     }

    #endregion
}
