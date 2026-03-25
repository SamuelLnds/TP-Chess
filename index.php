<?php

use Chess\Position;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;

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