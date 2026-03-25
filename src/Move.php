<?php

namespace Chess;

// Représente un déplacement sans validation
class Move
{
    #region Attributs

    private Position $from;
    private Position $to;

    #endregion

    #region Constructeur

    public function __construct(Position $from, Position $to)
    {
        $this->from = $from;
        $this->to = $to;
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

    #endregion
}
