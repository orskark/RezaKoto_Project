<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;

class StockMovementService
{
    public function applyMovement(StockMovement $movement): void
    {
        $stock = Stock::findOrFail($movement->stock_id);

        if ($movement->movement_type_id === 2 && $stock->quantity < $movement->quantity) {
            throw new \Exception('No hay suficiente stock para realizar la salida.');
        }

        $adjustment = $this->getAdjustmentValue($movement->movement_type_id, $movement->quantity);

        $stock->quantity += $adjustment;
        $stock->save();
    }

    public function revertMovement(StockMovement $movement): void
    {
        $stock = Stock::findOrFail($movement->stock_id);

        $adjustment = $this->getAdjustmentValue($movement->movement_type_id, $movement->quantity);

        $stock->quantity -= $adjustment;
        $stock->save();
    }

    private function getAdjustmentValue(int $typeId, int $quantity): int
    {
        return match ($typeId) {
            1 => $quantity,      // Entrada
            2 => -$quantity,     // Salida
            default => 0,
        };
    }

    public function handleUpdatedMovement(StockMovement $oldMovement, StockMovement $newMovement): void
    {
        // Si cambió stock_id, revertir en el anterior y aplicar en el nuevo
        if ($oldMovement->stock_id !== $newMovement->stock_id) {
            $this->revertMovement($oldMovement);
            $this->applyMovement($newMovement);
        } else {
            // Mismo stock_id, verificar si cambió el tipo de movimiento o la cantidad
            if (
                $oldMovement->movement_type_id !== $newMovement->movement_type_id ||
                $oldMovement->quantity !== $newMovement->quantity
            ) {
                $this->revertMovement($oldMovement);
                $this->applyMovement($newMovement);
            }
        }
    }
}
