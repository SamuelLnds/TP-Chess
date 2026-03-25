<?php

namespace Chess\Exception;

class InvalidMoveException extends ChessException
{
    public function __construct(string $message = 'La forme du coup est interdite.')
    {
        parent::__construct($message);
    }
}
