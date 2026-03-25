<?php

namespace Chess\Factory;

use Chess\Enum\PieceType;
use Chess\Enum\PieceColor;
use Chess\Position;
use Chess\Piece\Piece;
use Chess\Piece\Pawn;
use Chess\Piece\Knight;
use Chess\Piece\Bishop;
use Chess\Piece\Rook;
use Chess\Piece\Queen;
use Chess\Piece\King;

class PieceFactory
{
    public function create(PieceType $type, PieceColor $color, Position $position): Piece
    {
        return match ($type) {
            PieceType::PAWN => new Pawn($color, $position),
            PieceType::KNIGHT => new Knight($color, $position),
            PieceType::BISHOP => new Bishop($color, $position),
            PieceType::ROOK => new Rook($color, $position),
            PieceType::QUEEN => new Queen($color, $position),
            PieceType::KING => new King($color, $position),
        };
    }
}
