<?php

namespace Chess\Exception;

class OccupiedByAllyException extends ChessException
{
    public function __construct(string $message = 'La case cible contient une pièce alliée.')
    {
        parent::__construct($message);
    }
}
