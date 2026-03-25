<?php

namespace Chess;

use InvalidArgumentException;

class Position
{
    #region Attributs

    private int $row;
    private int $column;

    #endregion

    #region Constructeur

    public function __construct(int $row, int $column)
    {
        $this->setRow($row);
        $this->setColumn($column);
    }

    #endregion

    #region Getters et Setters

    public function getRow(): int
    {
        return $this->row;
    }

    public function setRow(int $row): void
    {
        if ($row < 0 || $row > 7) {
            throw new InvalidArgumentException("La valeur doit être comprise entre 0 et 7");
        }

        $this->row = $row;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function setColumn(int $column): void
    {
        if ($column < 0 || $column > 7) {
            throw new InvalidArgumentException("La valeur doit être comprise entre 0 et 7");
        }

        $this->column = $column;
    }

    #endregion

    #region Méthodes d'instance

    public function equals(Position $other): bool
    {
        // détermine si la position envoyée est égale à la position comparée
        return $other->getRow() === $this->getRow() && $other->getColumn() === $this->getColumn();
    }

    public function toKey(): string
    {
        // doit retourner une chaîne du type "6:4"
        return implode(':', [$this->getRow(), $this->getColumn()]);
    }

    #endregion

    #region Méthodes statiques

    public static function fromKey(string $key): Position
    {
        // doit récupérer une key qu'on parse en une position
        [$row, $column] = explode(':', $key);
        return new Position((int)$row, (int)$column);
    }

    #endregion
}
