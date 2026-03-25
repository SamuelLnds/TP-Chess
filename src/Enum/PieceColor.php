<?php

namespace Chess\Enum;

enum PieceColor
{
    #region Valeurs de l'enum

    case WHITE;
    case BLACK;

    #endregion

    #region Méthodes

    public function opposite(): PieceColor
    {
        // si la couleur courante est white, passer en black, et inversement
        return match ($this) {
            self::WHITE => self::BLACK,
            self::BLACK => self::WHITE,
        };
    }

    #endregion
}
