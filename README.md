# TP-Chess

TP pour la POO avancée en PHP avec les échecs dans le cadre du cours de développement back-end et conception de M1.

## Commandes

Installer les dépendances :

```bash
composer install
```

Lancer les tests :

```bash
composer test
```

Executer index.php :

```bash
php index.php
```

Executer play.php (hors cadre) :

```bash
php play.php
```

Lint :

```bash
composer lint
```

Correction automatique du style :

```bash
composer lint:fix
```

## Classes principales

- ✅ `Position`
- ✅ `__construct()`
- ✅ `getRow()`
- ✅ `getColumn()`
- ✅ `equals()`
- ✅ `toKey()`
- ✅ `fromKey()`
- ✅ `Move`
- ✅ `__construct()`
- ✅ `getFrom()`
- ✅ `getTo()`
- ✅ `Board`
- ✅ `placePiece()`
- ✅ `getPieceAt()`
- ✅ `hasPieceAt()`
- ✅ `removePieceAt()`
- ✅ `movePiece()`
- ✅ `isPathClear()`
- ✅ `getPieces()`
- ✅ `getKingPosition()`
- ✅ `render()`
- ✅ `Game`
- ✅ `__construct()`
- ✅ `start()`
- ✅ `getBoard()`
- ✅ `getCurrentPlayer()`
- ✅ `play()`
- ✅ `isCheck()`
- ✅ `setupPieces()`
- ✅ `switchPlayer()`

## Pieces

- ✅ `Piece`
- ✅ `__construct()`
- ✅ `getColor()`
- ✅ `getPosition()`
- ✅ `setPosition()`
- ✅ `getType()`
- ✅ `render()`
- ✅ `canMove()`
- ✅ `isValidMovementShape()`
- ✅ `canCapture()`
- ✅ `King`
- ✅ `isValidMovementShape()`
- ✅ `Queen`
- ✅ `isValidMovementShape()`
- ✅ `Rook`
- ✅ `isValidMovementShape()`
- ✅ `Bishop`
- ✅ `isValidMovementShape()`
- ✅ `Knight`
- ✅ `isValidMovementShape()`
- ✅ `Pawn`
- ✅ `isValidMovementShape()`

## Factory

- ✅ `PieceFactory`
- ✅ `create()`

## Interface / Enums

- ✅ `Renderable`
- ✅ `render()`
- ✅ `PieceColor`
- ✅ `WHITE`
- ✅ `BLACK`
- ✅ `opposite()`
- ✅ `PieceType`
- ✅ `KING`
- ✅ `QUEEN`
- ✅ `ROOK`
- ✅ `BISHOP`
- ✅ `KNIGHT`
- ✅ `PAWN`

## Exceptions

- ✅ `ChessException`
- ✅ `InvalidMoveException`
- ✅ `NoPieceException`
- ✅ `WrongTurnException`
- ✅ `OccupiedByAllyException`

## Bonus

- ✅ Roque
- ✅ Promotion du pion
- ✅ Prise en passant
- ✅ Interdiction de mettre son propre roi en echec
- ✅ Echec et mat
- ✅ Pat
- ❌ Historique complet des coups
- ✅ Tests automatises
- ❌ Autre bonus : a preciser
