<?php

use Chess\Position;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Board;
use Chess\Factory\PieceFactory;
use Chess\Move;
use Chess\Piece\Pawn;
use Chess\Piece\Knight;
use Chess\Piece\Bishop;
use Chess\Piece\Rook;
use Chess\Piece\Queen;
use Chess\Piece\King;
use Chess\Game;
use Chess\Piece\Piece;

require_once __DIR__ . '/vendor/autoload.php';

$test_title_frame = str_repeat('=', 40);

#region Tests Position.php

echo "\n$test_title_frame\n";
echo "Test Position.php";
echo "\n$test_title_frame\n";

try {
    $position = new Position(6, 4);
    echo "Position: " . $position->toKey() . "\n"; // Position: 6:4
    $position = Position::fromKey('6:4');
    echo "Row: " . $position->getRow() . ", Column: " . $position->getColumn() . "\n"; // Position: 6:4
    $position = new Position(-1, 2);
    echo "Position: " . $position->toKey() . "\n"; // Erreur
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests PieceColor.php

echo "\n$test_title_frame\n";
echo "Test PieceColor.php";
echo "\n$test_title_frame\n";

try {
    $color = PieceColor::WHITE;
    echo "Color : " . $color->name . "\n"; // Color : WHITE
    echo "Inversion : " . $color->opposite()->name; // Color : BLACK
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests PieceType.php

echo "\n$test_title_frame\n";
echo "Test PieceType.php";
echo "\n$test_title_frame\n";

try {
    // Ici l'array est inversé pour avoir l'ordre selon la hiérarchie des pièces (inverse de l'enum)
    foreach (array_reverse(PieceType::cases()) as $type) {
        echo "Type : " . $type->name . "\n"; // PAWN, KNIGHT, BISHOP, ROOK, QUEEN, KING
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests Piece.php

echo "\n$test_title_frame\n";
echo "Test Piece.php";
echo "\n$test_title_frame\n";

try {
    $board = new Board();
    $pawn = new Pawn(PieceColor::WHITE, new Position(6, 4));

    echo "Type : " . $pawn->getType()->name . "\n"; // PAWN
    echo "Couleur : " . $pawn->getColor()->name . "\n"; // WHITE
    echo "Direction : " . $pawn->getDirection() . "\n"; // -1

    $moveOk = $pawn->canMove($board, new Position(5, 4));
    echo "Déplacement (6,4 -> 5,4) : " . ($moveOk ? 'OK' : 'KO') . "\n"; // OK

    $moveKo = $pawn->canMove($board, new Position(6, 4));
    echo "Déplacement (6,4 -> 6,4) : " . ($moveKo ? 'OK' : 'KO') . "\n"; // KO
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests Move.php

echo "\n$test_title_frame\n";
echo "Test Move.php";
echo "\n$test_title_frame\n";

try {
    $move = new Move(new Position(6, 4), new Position(5, 4));
    echo "Move from " . $move->getFrom()->toKey() . " to " . $move->getTo()->toKey(); // Move de 6:4 à 5:4
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests Board.php

echo "\n$test_title_frame\n";
echo "Test Board.php";
echo "\n$test_title_frame\n";

try {
    $board = new Board();
    $board->placePiece(new Pawn(PieceColor::WHITE, new Position(6, 1)));
    $board->placePiece(new Knight(PieceColor::BLACK, new Position(6, 2)));
    $board->placePiece(new Pawn(PieceColor::BLACK, new Position(6, 3)));
    $board->placePiece(new Rook(PieceColor::WHITE, new Position(6, 4)));

    echo "Pièces sur le plateau :\n";
    foreach ($board->getPieces() as $position => $piece) {
        [$row, $col] = array_map('intval', explode(':', $position));
        echo $piece->render() . " ({$row},{$col})\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests PieceFactory.php

echo "\n$test_title_frame\n";
echo "Test PieceFactory.php";
echo "\n$test_title_frame\n";

try {
    $factory = new PieceFactory();
    $pawn = $factory->create(PieceType::PAWN, PieceColor::WHITE, new Position(6, 4));
    echo "Création d'un " . $pawn->render() . " à la position " . $pawn->getPosition()->toKey(); // Création d'un P à la position 6:4
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Tests représentation visuelle du plateau

echo "\n$test_title_frame\n";
echo "Test représentation visuelle du plateau";
echo "\n$test_title_frame\n";

try {
    $board = new Board();
    $board->placePiece(new Rook(PieceColor::WHITE, new Position(7, 0)));
    $board->placePiece(new Knight(PieceColor::WHITE, new Position(7, 1)));
    $board->placePiece(new Bishop(PieceColor::WHITE, new Position(7, 2)));
    $board->placePiece(new Queen(PieceColor::WHITE, new Position(7, 3)));
    $board->placePiece(new King(PieceColor::WHITE, new Position(7, 4)));
    $board->placePiece(new Bishop(PieceColor::WHITE, new Position(7, 5)));
    $board->placePiece(new Knight(PieceColor::WHITE, new Position(7, 6)));
    $board->placePiece(new Rook(PieceColor::WHITE, new Position(7, 7)));

    $board->placePiece(new Rook(PieceColor::BLACK, new Position(0, 0)));
    $board->placePiece(new Knight(PieceColor::BLACK, new Position(0, 1)));
    $board->placePiece(new Bishop(PieceColor::BLACK, new Position(0, 2)));
    $board->placePiece(new Queen(PieceColor::BLACK, new Position(0, 3)));
    $board->placePiece(new King(PieceColor::BLACK, new Position(0, 4)));
    $board->placePiece(new Bishop(PieceColor::BLACK, new Position(0, 5)));
    $board->placePiece(new Knight(PieceColor::BLACK, new Position(0, 6)));
    $board->placePiece(new Rook(PieceColor::BLACK, new Position(0, 7)));

    for ($col = 0; $col < 8; $col++) {
        $board->placePiece(new Pawn(PieceColor::WHITE, new Position(6, $col)));
        $board->placePiece(new Pawn(PieceColor::BLACK, new Position(1, $col)));
    }

    echo "Plateau de jeu :\n\n";
    echo $board->render();
} catch (Exception $e) {
    echo $e->getMessage();
}

#endregion

echo "\n";

#region Zone de test de jeu

echo "\n$test_title_frame\n";
echo "Zone de test de jeu";
echo "\n$test_title_frame\n";

try {
    $game = new Game();
    
    // Début de partie
    echo "Démarrage de la partie...\n\n";
    $game->start();
    echo $game->getBoard()->render();
    echo "Le joueur " . $game->getCurrentPlayer()->name . " commence.\n";

    // simulation d'un faux déplacement
    testMove($game, new Move(new Position(6, 4), new Position(0, 4))); // déplacement du pion blanc interdit
    testMove($game, new Move(new Position(6, 4), new Position(4, 4))); // déplacement du pion blanc autorisé
    testMove($game, new Move(new Position(1, 4), new Position(3, 4))); // déplacement du pion noir autorisé
    testMove($game, new Move(new Position(4, 4), new Position(3, 4))); // mauvaise tentative de capture
    testMove($game, new Move(new Position(6, 3), new Position(4, 3))); // déplacement du pion blanc autorisé
    testMove($game, new Move(new Position(3, 4), new Position(4, 3))); // bonne tentative de capture
    testMove($game, new Move(new Position(4, 4), new Position(3, 4))); // déplacement du pion blanc autorisé
    testMove($game, new Move(new Position(3, 4), new Position(2, 4))); // déplacement du pion blanc interdit
    testMove($game, new Move(new Position(4,3), new Position(5, 3))); // déplacement du pion noir autorisé
    testMove($game, new Move(new Position(3, 4), new Position(2, 4))); // déplacement du pion blanc autorisé
    testMove($game, new Move(new Position(5,3), new Position(6, 3))); // déplacement du pion noir autorisé
} catch (Exception $e) {
    echo $e->getMessage();
}

function testMove(Game $game, Move $move): void
{
    try {
        echo "Déplacement de " . $move->getFrom()->keyChessNotation() . " à " . $move->getTo()->keyChessNotation() . ".\n\n";
        $game->play($move);
        if ($game->isCheck($game->getCurrentPlayer()))
        {
            echo "Échec au roi " . $game->getCurrentPlayer()->name . " !\n\n";
        }
        echo $game->getBoard()->render();
    } catch (Exception $e) {
        echo "Erreur lors du déplacement de " . $move->getFrom()->keyChessNotation() . " à " . $move->getTo()->keyChessNotation() . " : " . $e->getMessage() . "\n\n";
    }
}

#endregion
