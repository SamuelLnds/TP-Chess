<?php

// Rendering de board différent de la classe parce que je voulais quelque chose de plus esthétique et celui-ci est généré par IA
// Idem pour le changement de l'apparence des pièces
// play.php ne rentre pas dans le cadre du TP mais était surtout une expérience pour manipuler davantage,
// expliquant la volonté de séparer une partie de la logique afin de ne pas entacher le travail qui a été réalisé

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Chess\Board;
use Chess\Enum\PieceColor;
use Chess\Enum\PieceType;
use Chess\Exception\ChessException;
use Chess\Game;
use Chess\Move;
use Chess\Position;

#region Constantes ANSI

// Les séquences ANSI sont des codes d'échappement interprétés par le terminal pour
// mettre en couleur ou en style du texte. Le format de base est \033[Nm où N est le
// numéro du code. Pour les couleurs de fond/texte 256, c'est \033[48;5;Nm (fond) ou
// \033[38;5;Nm (texte). Ref : https://en.wikipedia.org/wiki/ANSI_escape_code

const RESET    = "\033[0m";
const BOLD     = "\033[1m";
const DIM      = "\033[90m";
const RED      = "\033[31m";
const YELLOW   = "\033[1;33m";
const GREEN    = "\033[1;32m";
const BG_LIGHT = "\033[48;5;222m";
const BG_DARK  = "\033[48;5;94m";
const W_PIECE  = "\033[1;97m";
const B_PIECE  = "\033[38;5;232m";

const PIECES = [
    'K' => "♔", 'Q' => "♕", 'R' => "♖", 'B' => "♗", 'N' => "♘", 'P' => "♙",
    'k' => "♚", 'q' => "♛", 'r' => "♜", 'b' => "♝", 'n' => "♞", 'p' => "♟",
];

#endregion

#region Affichage

// 
function renderBoard(Board $board): void
{
    $cols = DIM . '      a  b  c  d  e  f  g  h' . RESET;
    echo "\n{$cols}\n";

    for ($row = 0; $row < 8; $row++) {
        $rank = 8 - $row;
        echo DIM . "  {$rank}  " . RESET;

        for ($col = 0; $col < 8; $col++) {
            $bg    = ($row + $col) % 2 === 0 ? BG_LIGHT : BG_DARK;
            $piece = $board->getPieceAt(new Position($row, $col));

            if ($piece !== null) {
                $fg = $piece->getColor() === PieceColor::WHITE ? W_PIECE : B_PIECE;
                echo $bg . $fg . ' ' . PIECES[$piece->render()] . ' ' . RESET;
            } else {
                echo $bg . '   ' . RESET;
            }
        }

        echo DIM . "  {$rank}" . RESET . "\n";
    }

    echo "{$cols}\n\n";
}

#endregion

#region Saisie

function readInput(string $prompt): string
{
    echo $prompt;
    $line = fgets(STDIN);
    return $line === false ? '' : trim($line);
}

function parseSquare(string $sq): Position
{
    return new Position(8 - (int) $sq[1], ord($sq[0]) - ord('a'));
}

function parseMove(string $raw, PieceColor $player): Move
{
    $s = strtolower(str_replace([' ', '.'], '', trim($raw)));

    // roque
    if ($s === 'o-o-o' || $s === '0-0-0') {
        $row = $player === PieceColor::WHITE ? 7 : 0;
        return new Move(new Position($row, 4), new Position($row, 2));
    }
    if ($s === 'o-o' || $s === '0-0') {
        $row = $player === PieceColor::WHITE ? 7 : 0;
        return new Move(new Position($row, 4), new Position($row, 6));
    }

    // déplacement : e2e4  e2-e4  e7e8q
    $s = str_replace('-', '', $s);
    if (!preg_match('/^([a-h][1-8])([a-h][1-8])([qrbn]?)$/', $s, $m)) {
        throw new \InvalidArgumentException(
            'Format non reconnu — exemples : e2e4  e2-e4  O-O  O-O-O  e7e8q'
        );
    }

    $promotion = match ($m[3]) {
        'q'     => PieceType::QUEEN,
        'r'     => PieceType::ROOK,
        'b'     => PieceType::BISHOP,
        'n'     => PieceType::KNIGHT,
        default => null,
    };

    return new Move(parseSquare($m[1]), parseSquare($m[2]), $promotion);
}

// Demande la pièce de promotion si le joueur ne l'a pas précisée dans sa saisie
function resolvePromotion(Move $move, Board $board, PieceColor $player): Move
{
    $src = $board->getPieceAt($move->getFrom());
    if (
        $src === null
        || $src->getType() !== PieceType::PAWN
        || $move->getPromotionChoice() !== null
    ) {
        return $move;
    }

    $promotionRow = $player === PieceColor::WHITE ? 0 : 7;
    if ($move->getTo()->getRow() !== $promotionRow) {
        return $move;
    }

    $choice = readInput('  Promotion — pièce (d=dame  r=tour  f=fou  c=cavalier) > ');
    $type   = match (strtolower(trim($choice))) {
        'r'     => PieceType::ROOK,
        'f'     => PieceType::BISHOP,
        'c'     => PieceType::KNIGHT,
        default => PieceType::QUEEN,
    };

    return new Move($move->getFrom(), $move->getTo(), $type);
}

#endregion

#region Boucle de jeu

echo RESET . BOLD . "\n  ♟  Échecs — deux joueurs\n" . RESET;
echo DIM . "  Coups : e2e4  e2-e4  O-O  O-O-O  e7e8q\n\n" . RESET;

$nameWhite = readInput('  Nom des Blancs ♙ > ');
if ($nameWhite === '') {
    $nameWhite = 'Blancs';
}
$nameBlack = readInput('  Nom des Noirs  ♟ > ');
if ($nameBlack === '') {
    $nameBlack = 'Noirs';
}
echo "\n";

do {
    $game = new Game();
    $game->start();

    while (true) {
        renderBoard($game->getBoard());

        $current   = $game->getCurrentPlayer();
        $isWhite   = $current === PieceColor::WHITE;
        $name      = $isWhite ? $nameWhite : $nameBlack;
        $opponent  = $isWhite ? $nameBlack : $nameWhite;
        $icon      = $isWhite ? '♙' : '♟';

        if ($game->isCheckmate($current)) {
            echo GREEN . "  Échec et mat ! {$opponent} remporte la partie.\n" . RESET . "\n";
            break;
        }

        if ($game->isStalemate($current)) {
            echo YELLOW . "  Pat — partie nulle.\n" . RESET . "\n";
            break;
        }

        $prefix = $game->isCheck($current)
            ? YELLOW . '  ⚠ Échec !  ' . RESET
            : '  ';

        $raw = readInput("{$prefix}{$name} {$icon} > ");

        if ($raw === '') {
            continue;
        }

        try {
            $move = parseMove($raw, $current);
            $move = resolvePromotion($move, $game->getBoard(), $current);
            $game->play($move);
        } catch (ChessException $e) {
            echo RED . '  Coup invalide : ' . $e->getMessage() . RESET . "\n";
        } catch (\InvalidArgumentException $e) {
            echo RED . '  ' . $e->getMessage() . RESET . "\n";
        }
    }

    $again = readInput('  Nouvelle partie ? (o/n) > ');
} while ($again === 'o');

echo "\n  À bientôt !\n\n";

#endregion
