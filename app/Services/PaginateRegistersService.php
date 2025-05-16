<?php

namespace App\Services;

use App\DTOs\FilterDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class PaginateRegistersService
{
    public function execute(Builder $query, FilterDTO $dto, array $searchables = [], array $filterables = [], array $relationSearchables = [])
    {
        $model = $query->getModel();

        // Búsqueda (local + relacional)
        if ($dto->search) {
            $query->where(function (Builder $q) use ($dto, $searchables, $relationSearchables, $model) {
                foreach ($searchables as $column) {
                    if ($this->isColumnValid($model, $column)) {
                        $q->orWhere($column, 'like', '%' . $dto->search . '%');
                    }
                }

                foreach ($relationSearchables as $relation => $columns) {
                    $q->orWhereHas($relation, function (Builder $subQ) use ($columns, $dto) {
                        foreach ((array) $columns as $column) {
                            $subQ->where($column, 'like', '%' . $dto->search . '%');
                        }
                    });
                }
            });
        }

        // Filtros
        foreach ($dto->filters as $key => $value) {
            if (in_array($key, $filterables) && $this->isColumnValid($model, $key)) {
                $query->where($key, $value);
            }
        }

        // Ordenamiento
        if ($dto->orderBy && $this->isColumnValid($model, $dto->orderBy)) {
            $query->orderBy($dto->orderBy, $dto->orderDir);
        }

        // Paginación
        return $query->paginate($dto->perPage, ['*'], 'page', $dto->page);
    }

    protected function isColumnValid($model, string $column): bool
    {
        return Schema::hasColumn($model->getTable(), $column);
    }
}