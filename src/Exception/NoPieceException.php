<?php

namespace Chess\Exception;

class NoPieceException extends ChessException
{
    public function __construct(string $message = 'Aucune pièce n\'existe sur la case source.')
    {
        parent::__construct($message);
    }
}
