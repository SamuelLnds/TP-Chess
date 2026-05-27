<?php

namespace Chess;

use Chess\Contract\Renderable;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Exception\NoPieceException;
use Chess\Exception\InvalidMoveException;
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

        if (!$piece->canMove($this, $to)) {
            throw new InvalidMoveException();
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

    // récupère les pièces d'une certaine couleur
        /**
     * @return array<string, Piece>  clé "row:col" => pièce
     */
    public function getPiecesFromColor(PieceColor $color): array
    {
        // initialisation du tableau
        $piecesOfColor = [];
        // toutes les pièces
        foreach ($this->getPieces() as $piece) {
            if ($piece->getColor() === $color) {
                array_push($piecesOfColor, $piece);
            }
        }

        return $piecesOfColor;
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
        $lines = [];

        // parcourt toutes les lignes
        for ($row = 0; $row < 8; $row++) {
            $cells = [];

            // parcourt toutes les colonnes
            for ($col = 0; $col < 8; $col++) {
                // récupère la pièce à la position courante
                $piece = $this->getPieceAt(new Position($row, $col));
                $cells[] = $piece ? $piece->render() : '.';
            }

            // ajoute la ligne au tableau de lignes
            $lines[] = implode(' ', $cells);
        }

        // construit tout le tableau contenant les lignes et les numéros de ligne/colonne
        $rowNumbers = range(1, 8);
        $colLetters = range('a', 'h');
        $colWidth = 4;

        $board = str_repeat(' ', $colWidth) . implode(' ', $colLetters) . "\n"; // en-tête des colonnes
        $board .= '  ┌' . str_repeat('─', count($rowNumbers) * 2 + 1) . '┐' . "\n"; // séparateur
        foreach ($lines as $index => $line) {
            $board .= $rowNumbers[$index] . ' │ ' . $line . ' │ ' . $rowNumbers[$index] . "\n"; // numéro de ligne + contenu de la ligne
        }
        $board .= '  └' . str_repeat('─', count($rowNumbers) * 2 + 1) . '┘' . "\n"; // séparateur
        $board .= str_repeat(' ', $colWidth) . implode(' ', $colLetters) . "\n"; // en-tête des colonnes

        // PHP_EOL est utilisé ici comme séparateur pour un bon retour à la ligne
        return $board . PHP_EOL;
    }

    #endregion
}
