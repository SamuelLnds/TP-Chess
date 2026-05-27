<?php

namespace Chess\Piece;

use Chess\Contract\Renderable;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Exception\InvalidMoveException;
use Chess\Exception\OccupiedByAllyException;
use Chess\Exception\ChessException;
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
        if (!$this->isNotSameSquare($target) || !$this->isValidMovementShape($target)) {
            throw new InvalidMoveException();
        }

        if ($this->isTargetOccupiedByAlly($board, $target)) {
            // lever OccupiedByAllyException si nécessaire
            throw new OccupiedByAllyException();
        }

        // Un pion ne peut se déplacer en diagonale que pour capturer
        if ($this instanceof Pawn && $this->getPosition()->getColumn() !== $target->getColumn() && !$board->hasPieceAt($target)) {
            throw new InvalidMoveException();
        }

        if (!$this instanceof Knight && !$board->isPathClear($this->getPosition(), $target)) {
            throw new InvalidMoveException();
        }

        if ($board->hasPieceAt($target)) {
            // également prendre en compte le chemin obstrué en cas de capture
            if (!$this instanceof Knight && !$board->isPathClear($this->getPosition(), $target)) {
                throw new InvalidMoveException();
            }
            return $this->canCapture($board, $target);
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

        // tout droit est un mouvement valide pour un pion mais pas pour une capture
        if ($this instanceof Pawn && $this->getPosition()->getColumn() === $target->getColumn()) {
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

    #region Méthodes de vérification

    private function isNotSameSquare(Position $target): bool
    {
        return !$this->getPosition()->equals($target);
    }

    private function isTargetOccupiedByAlly(Board $board, Position $target): bool
    {
        $piece = $board->getPieceAt($target);
        return $piece !== null && $piece->getColor() === $this->getColor();
    }

    #endregion
}
