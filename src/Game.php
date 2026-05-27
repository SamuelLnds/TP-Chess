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
        if (!$this->board->hasPieceAt($move->getFrom()) || $this->board->getPieceAt($move->getFrom()) === null) {
            // lever NoPieceException si nécessaire
            throw new NoPieceException("Aucune pièce à la position " . $move->getFrom()->toKey());
        }

        $piece = $this->board->getPieceAt($move->getFrom());

        // vérifier le tour du joueur
        if ($piece->getColor() !== $this->currentPlayer) {
            // lever WrongTurnException si nécessaire
            throw new WrongTurnException("C'est au tour de " . $this->currentPlayer->name);
        }

        // vérifier le déplacement via la pièce
        if (!$piece->canMove($this->board, $move->getTo())) {
            // lever InvalidMoveException si nécessaire
            throw new InvalidMoveException(
                "Déplacement invalide de " . $piece->render() .
                " de " . $move->getFrom()->toKey() . " à " . $move->getTo()->toKey()
            );
        }

        // déplacer la pièce
        $this->board->movePiece($move->getFrom(), $move->getTo());

        // rollback le déplacement si mise en échec
        if ($this->isCheck(($this->currentPlayer))) {

            // movePiece possède des règles internes et ne peut pas être réutilisé
            // donc on fait manuellement
            $this->board->removePieceAt($move->getTo());
            $piece->setPosition($move->getFrom());
            $this->board->placePiece($piece);

            throw new InvalidMoveException("Ce déplacement met votre roi en échec.");
        }

        // changer le joueur courant
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
                if ($piece->canMove($this->board, $king_position)) {
                    echo "Le roi de couleur " . $color->name . " est en échec par " . $piece->render() . " en " . $piece->getPosition()->toKey() . "\n";
                    return true;
                }
            } catch (ChessException $e) {
                // une exception signifie juste que le déplacement est impossible
                continue;
            }
        }

        return false;
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
