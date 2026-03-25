<?php

namespace Chess;

use Chess\Contract\Renderable;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Exception\NoPieceException;
use Chess\Piece\Piece;

class Board implements Renderable
{
    #region Attributs

    // Le tableau doit être indexé par la clé retournée par Position::toKey()
    /** @var array<string, Piece> */
    private array $pieces = [];

    #endregion

    #region Méthodes

    // pose ou remplace une pièce sur la case
    public function placePiece(Piece $piece): void
    {
        $key = $piece->getPosition()->toKey();
        $this->pieces[$key] = $piece;
    }

    public function getPieceAt(Position $position): ?Piece
    {
        $key = $position->toKey();
        return $this->pieces[$key] ?? null;
    }

    public function hasPieceAt(Position $position): bool
    {
        return $this->getPieceAt($position) !== null;
    }

    public function removePieceAt(Position $position): void
    {
        $key = $position->toKey();
        unset($this->pieces[$key]);
    }

    // déplace réellement la pièce dans le tableau
    public function movePiece(Position $from, Position $to): void
    {
        $piece = $this->getPieceAt($from);

        if ($piece === null) {
            throw new NoPieceException();
        }

        $this->removePieceAt($from);
        $piece->setPosition($to);
        $this->placePiece($piece);
    }

    // vérifie uniquement les cases intermédiaires
    public function isPathClear(Position $from, Position $to): bool
    {
        // Calcule la différence entre les lignes et les colonnes
        $rowDiff = $to->getRow() - $from->getRow();
        $colDiff = $to->getColumn() - $from->getColumn();

        // Détermine le sens du déplacement
        if ($rowDiff === 0) {
            $rowStep = 0;
        } elseif ($rowDiff > 0) {
            $rowStep = 1;
        } else {
            $rowStep = -1;
        }

        if ($colDiff === 0) {
            $colStep = 0;
        } elseif ($colDiff > 0) {
            $colStep = 1;
        } else {
            $colStep = -1;
        }

        // Initialise la position courante à la première case intermédiaire
        $currentRow = $from->getRow() + $rowStep;
        $currentCol = $from->getColumn() + $colStep;

        // Parcourt toutes les cases intermédiaires jusqu'à la destination
        while ($currentRow !== $to->getRow() || $currentCol !== $to->getColumn()) {
            // Si une pièce est présente, le chemin n'est pas dégagé
            if ($this->hasPieceAt(new Position($currentRow, $currentCol))) {
                return false;
            }
            // Avance vers la prochaine case
            $currentRow += $rowStep;
            $currentCol += $colStep;
        }

        // Le chemin est dégagé
        return true;
    }

    // doit retourner un tableau de Piece
    /**
     * @return array<string, Piece>  clé "row:col" => pièce
     */
    public function getPieces(): array
    {
        return $this->pieces;
    }

    public function getKingPosition(PieceColor $color): ?Position
    {
        foreach ($this->getPieces() as $piece) {
            if ($piece->getType() === PieceType::KING && $piece->getColor() === $color) {
                return $piece->getPosition();
            }
        }
        return null;
    }

    #endregion

    #region Implémentation Renderable

    // doit retourner une représentation texte du plateau
    public function render(): string
    {
        return '';
    }

    #endregion
}
