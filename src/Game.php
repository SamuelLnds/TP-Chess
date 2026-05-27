<?php

namespace Chess;

use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Exception\ChessException;
use Chess\Exception\InvalidMoveException;
use Chess\Exception\NoPieceException;
use Chess\Exception\WrongTurnException;
use Chess\Factory\PieceFactory;
use Chess\Move;
use Chess\Piece\Piece;

class Game
{
    #region Attributs

    private Board $board;
    private PieceColor $currentPlayer;
    private PieceFactory $pieceFactory;

    #endregion

    #region Constructeur

    public function __construct()
    {
        // créer le plateau
        $this->board = new Board();
        // initialiser le joueur courant à WHITE
        $this->currentPlayer = PieceColor::WHITE;
        // appeler la factory pour créer les pièces
        $this->pieceFactory = new PieceFactory();
    }

    #endregion

    #region Méthodes

    public function start(): void
    {
        // placer les pièces au démarrage
        $this->setupPieces();
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getCurrentPlayer(): PieceColor
    {
        return $this->currentPlayer;
    }

    public function play(Move $move): void
    {
        // récupérer la pièce source
        $piece = $this->board->getPieceAt($move->getFrom());
        if ($piece === null) {
            throw new NoPieceException("Aucune pièce à la position " . $move->getFrom()->toKey());
        }

        // vérifier le tour du joueur
        if ($piece->getColor() !== $this->currentPlayer) {
            throw new WrongTurnException("C'est au tour de " . $this->currentPlayer->name);
        }

        $isCastling = $piece->getType() === PieceType::KING
            && abs($move->getTo()->getColumn() - $move->getFrom()->getColumn()) === 2;

        if ($isCastling) {
            if ($this->isCheck($this->currentPlayer)) {
                throw new InvalidMoveException("Impossible de roquer lorsque le roi est en échec.");
            }
            $this->validateCastling($piece, $move);
        }

        // vérifier le déplacement via la pièce
        try {
            $piece->canMove($this->board, $move->getTo());
        } catch (ChessException $e) {
            throw new InvalidMoveException(
                "Déplacement invalide de " . $piece->render() .
                " de " . $move->getFrom()->toKey() . " à " . $move->getTo()->toKey()
            );
        }

        // prise en passant — retirer le pion capturé avant de tester l'échec
        $enPassantTarget = $this->board->getEnPassantTarget();
        $isEnPassant = $piece->getType() === PieceType::PAWN
            && $enPassantTarget !== null
            && $move->getTo()->equals($enPassantTarget);
        $enPassantPawn = null;
        if ($isEnPassant) {
            $enPassantPawnPos = new Position($move->getFrom()->getRow(), $move->getTo()->getColumn());
            $enPassantPawn = $this->board->getPieceAt($enPassantPawnPos);
            $this->board->removePieceAt($enPassantPawnPos);
        }

        $captured = $this->simulateMove($move);

        if ($this->isCheck($this->currentPlayer)) {
            $this->undoMove($move, $piece, $captured);
            if ($isEnPassant && $enPassantPawn !== null) {
                $this->board->placePiece($enPassantPawn);
            }
            throw new InvalidMoveException("Ce déplacement met votre roi en échec.");
        }

        // mise à jour de la cible en passant pour le prochain coup
        $isDoublePawnMove = $piece->getType() === PieceType::PAWN
            && abs($move->getTo()->getRow() - $move->getFrom()->getRow()) === 2;
        if ($isDoublePawnMove) {
            $passedRow = intdiv($move->getFrom()->getRow() + $move->getTo()->getRow(), 2);
            $this->board->setEnPassantTarget(new Position($passedRow, $move->getFrom()->getColumn()));
        } else {
            $this->board->setEnPassantTarget(null);
        }

        // promotion
        $wasPromoted = false;
        if ($piece->getType() === PieceType::PAWN) {
            $promotionRow = $piece->getColor() === PieceColor::WHITE ? 0 : 7;
            if ($piece->getPosition()->getRow() === $promotionRow) {
                $wasPromoted = true;
                $promotionType = $move->getPromotionChoice() ?? PieceType::QUEEN;
                $promoted = $this->pieceFactory->create($promotionType, $piece->getColor(), $piece->getPosition());
                $promoted->markAsMoved();
                $this->board->removePieceAt($piece->getPosition());
                $this->board->placePiece($promoted);
            }
        }

        if ($isCastling) {
            $this->executeCastlingRookMove($move);
        }

        if (!$wasPromoted) {
            $piece->markAsMoved();
        }
        $this->switchPlayer();
    }

    public function isCheck(PieceColor $color): bool
    {
        // retrouver la position du roi de la couleur demandée
        $king_position = $this->board->getKingPosition($color);

        if ($king_position == null) {
            throw new NoPieceException("Le roi de la couleur " . $color->name . " est introuvable sur le plateau.");
        }

        // récupérer toutes les pièces adverses
        $opponent_pieces = $this->board->getPiecesFromColor($color->opposite());
        // tester si l'une d'elles peut atteindre la case du roi
        foreach ($opponent_pieces as $piece) {
            try {
                // retourner true dès qu'une menace existe
                $piece->canMove($this->board, $king_position);
                return true;
            } catch (ChessException $e) {
                // une exception signifie juste que le déplacement est impossible
                continue;
            }
        }

        return false;
    }

    public function isCheckmate(PieceColor $color): bool
    {
        return $this->isCheck($color) && !$this->hasLegalMoves($color);
    }

    public function isStalemate(PieceColor $color): bool
    {
        return !$this->isCheck($color) && !$this->hasLegalMoves($color);
    }

    private function hasLegalMoves(PieceColor $color): bool
    {
        $pieces = $this->board->getPiecesFromColor($color);
        $currentlyInCheck = $this->isCheck($color);

        for ($row = 0; $row < 8; $row++) {
            for ($col = 0; $col < 8; $col++) {
                $target = new Position($row, $col);
                foreach ($pieces as $piece) {
                    // Roque non autorisé si le roi est en échec
                    $isCastlingAttempt = $piece->getType() === PieceType::KING
                        && abs($target->getColumn() - $piece->getPosition()->getColumn()) === 2;
                    if ($isCastlingAttempt && $currentlyInCheck) {
                        continue;
                    }

                    try {
                        $piece->canMove($this->board, $target);
                    } catch (ChessException $e) {
                        continue;
                    }
                    $from = $piece->getPosition();
                    $simulatedMove = new Move($from, $target);

                    // prise en passant — retirer le pion capturé avant de simuler
                    $enPassantTarget = $this->board->getEnPassantTarget();
                    $isEnPassant = $piece->getType() === PieceType::PAWN
                        && $enPassantTarget !== null
                        && $target->equals($enPassantTarget);
                    $enPassantPawn = null;
                    if ($isEnPassant) {
                        $enPassantPawnPos = new Position($from->getRow(), $target->getColumn());
                        $enPassantPawn = $this->board->getPieceAt($enPassantPawnPos);
                        $this->board->removePieceAt($enPassantPawnPos);
                    }

                    $captured = $this->simulateMove($simulatedMove);
                    $inCheck = $this->isCheck($color);
                    $this->undoMove($simulatedMove, $piece, $captured);

                    if ($isEnPassant && $enPassantPawn !== null) {
                        $this->board->placePiece($enPassantPawn);
                    }

                    if (!$inCheck) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function simulateMove(Move $move): ?Piece
    {
        $captured = $this->board->getPieceAt($move->getTo());
        $this->board->movePiece($move->getFrom(), $move->getTo());
        return $captured;
    }

    private function undoMove(Move $move, Piece $moved, ?Piece $captured): void
    {
        $this->board->removePieceAt($move->getTo());
        $moved->setPosition($move->getFrom());
        $this->board->placePiece($moved);
        if ($captured !== null) {
            $this->board->placePiece($captured);
        }
    }

    private function validateCastling(Piece $king, Move $move): void
    {
        $row = $king->getPosition()->getRow();
        $isKingside = $move->getTo()->getColumn() > $move->getFrom()->getColumn();
        $rookCol = $isKingside ? 7 : 0;
        $passThroughCol = $isKingside ? 5 : 3;

        $rook = $this->board->getPieceAt(new Position($row, $rookCol));
        if ($rook === null || $rook->getType() !== PieceType::ROOK || $rook->hasMoved()) {
            throw new InvalidMoveException("Le roque est impossible.");
        }

        $minCol = min($king->getPosition()->getColumn(), $rookCol) + 1;
        $maxCol = max($king->getPosition()->getColumn(), $rookCol) - 1;
        for ($col = $minCol; $col <= $maxCol; $col++) {
            if ($this->board->hasPieceAt(new Position($row, $col))) {
                throw new InvalidMoveException("Le chemin pour le roque est obstrué.");
            }
        }

        $passThroughMove = new Move($king->getPosition(), new Position($row, $passThroughCol));
        $capturedAtPassThrough = $this->simulateMove($passThroughMove);
        $passedThroughCheck = $this->isCheck($king->getColor());
        $this->undoMove($passThroughMove, $king, $capturedAtPassThrough);
        if ($passedThroughCheck) {
            throw new InvalidMoveException("Le roi ne peut pas passer par une case en échec.");
        }
    }

    private function executeCastlingRookMove(Move $move): void
    {
        $row = $move->getTo()->getRow();
        $isKingside = $move->getTo()->getColumn() > $move->getFrom()->getColumn();
        $rookFromCol = $isKingside ? 7 : 0;
        $rookToCol = $isKingside ? 5 : 3;

        $rook = $this->board->getPieceAt(new Position($row, $rookFromCol));
        if ($rook !== null) {
            $this->board->removePieceAt(new Position($row, $rookFromCol));
            $rook->setPosition(new Position($row, $rookToCol));
            $this->board->placePiece($rook);
            $rook->markAsMoved();
        }
    }

    private function setupPieces(): void
    {
        // placement des pièces de la ligne du fond
        $this->board->placePiece($this->pieceFactory->create(PieceType::ROOK, PieceColor::WHITE, new Position(7, 0)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KNIGHT, PieceColor::WHITE, new Position(7, 1)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::BISHOP, PieceColor::WHITE, new Position(7, 2)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::QUEEN, PieceColor::WHITE, new Position(7, 3)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KING, PieceColor::WHITE, new Position(7, 4)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::BISHOP, PieceColor::WHITE, new Position(7, 5)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KNIGHT, PieceColor::WHITE, new Position(7, 6)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::ROOK, PieceColor::WHITE, new Position(7, 7)));

        $this->board->placePiece($this->pieceFactory->create(PieceType::ROOK, PieceColor::BLACK, new Position(0, 0)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KNIGHT, PieceColor::BLACK, new Position(0, 1)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::BISHOP, PieceColor::BLACK, new Position(0, 2)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::QUEEN, PieceColor::BLACK, new Position(0, 3)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KING, PieceColor::BLACK, new Position(0, 4)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::BISHOP, PieceColor::BLACK, new Position(0, 5)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::KNIGHT, PieceColor::BLACK, new Position(0, 6)));
        $this->board->placePiece($this->pieceFactory->create(PieceType::ROOK, PieceColor::BLACK, new Position(0, 7)));

        // placement des pions
        for ($col = 0; $col < 8; $col++) {
            $this->board->placePiece($this->pieceFactory->create(
                PieceType::PAWN,
                PieceColor::WHITE,
                new Position(6, $col)
            ));
            $this->board->placePiece($this->pieceFactory->create(
                PieceType::PAWN,
                PieceColor::BLACK,
                new Position(1, $col)
            ));
        }
    }

    private function switchPlayer(): void
    {
        $this->currentPlayer = $this->currentPlayer->opposite();
    }

    #endregion
}
