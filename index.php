<?php

use Chess\Position;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Board;
use Chess\Move;
use Chess\Piece\Pawn;
use Chess\Piece\Knight;
use Chess\Piece\Rook;

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