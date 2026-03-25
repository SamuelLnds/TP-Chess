<?php

namespace Chess\Exception;

class WrongTurnException extends ChessException
{
    public function __construct(string $message = 'Le joueur tente de jouer la mauvaise couleur.')
    {
        parent::__construct($message);
    }
}
