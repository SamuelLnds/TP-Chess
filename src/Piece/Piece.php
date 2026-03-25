<?php

namespace Chess\Piece;

use Chess\Contract\Renderable;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Position;
use Chess\Board;

abstract class Piece implements Renderable
{
    #region Attributs

    protected PieceColor $color;
    protected Position $position;
    protected PieceType $type;

    #endregion

    #region Constructeur

    public function __construct(PieceColor $color, Position $position, PieceType $type)
    {
        $this->color = $color;
        $this->position = $position;
        $this->type = $type;
    }

    #endregion

    #region Getters et Setters

    public function getColor(): PieceColor
    {
        return $this->color;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    public function getType(): PieceType
    {
        return $this->type;
    }

    #endregion

    #region Méthodes

    // Retourne une représentation textuelle de la pièce
    public function render(): string
    {
        $isWhite = $this->color === PieceColor::WHITE;

        return match ($this->type) {
            PieceType::KING => $isWhite ? 'K' : 'k',
            PieceType::QUEEN => $isWhite ? 'Q' : 'q',
            PieceType::ROOK => $isWhite ? 'R' : 'r',
            PieceType::BISHOP => $isWhite ? 'B' : 'b',
            PieceType::KNIGHT => $isWhite ? 'N' : 'n',
            PieceType::PAWN => $isWhite ? 'P' : 'p',
        };
    }

    #endregion

    #region Template methods

    public function canMove(Board $board, Position $target): bool
    {
        // la pièce ne reste pas sur place
        if ($this->getPosition()->equals($target)) {
            return false;
        }

        // la forme du déplacement est valide
        if (!$this->isValidMovementShape($target)) {
            return false;
        }

        // la case cible n'est pas occupée par un allié
        if ($board->isAllyAt($target, $this->getColor())) {
            return false;
        }

        // si la pièce n'est pas un cavalier, le chemin est libre
        if (!$this instanceof Knight) {
            return $board->isPathClear($this->getPosition(), $target);
        }

        return true;
    }

    protected function canCapture(Board $board, Position $target): bool
    {
        // il doit y avoir une pièce ennemie à capturer sur la case cible
        $targetPiece = $board->getPieceAt($target);
        if ($targetPiece === null || $targetPiece->getColor() === $this->getColor()) {
            return false;
        }

        return true;
    }

    abstract protected function isValidMovementShape(Position $target): bool;

    // Récupération de la direction selon la couleur
    public function getDirection(): int
    {
        return $this->getColor() === PieceColor::WHITE ? -1 : 1;
    }

    #endregion
}
