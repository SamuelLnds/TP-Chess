<?php

namespace Chess;

use Chess\Enum\PieceType;

// Représente un déplacement sans validation
class Move
{
    #region Attributs

    private Position $from;
    private Position $to;
    private ?PieceType $promotionChoice;

    #endregion

    #region Constructeur

    public function __construct(Position $from, Position $to, ?PieceType $promotionChoice = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->promotionChoice = $promotionChoice;
    }

    #endregion

    #region Getters

    public function getFrom(): Position
    {
        return $this->from;
    }

    public function getTo(): Position
    {
        return $this->to;
    }

    public function getPromotionChoice(): ?PieceType
    {
        return $this->promotionChoice;
    }

    #endregion
}
