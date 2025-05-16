<?php

namespace App\DTOs;

class FilterDTO
{
    /**
     * @param string|null $search Búsqueda general.
     * @param int $page Página actual (mínimo 1).
     * @param int $perPage Registros por página (mínimo 1, máximo 100).
     * @param string|null $orderBy Columna para ordenar.
     * @param string $orderDir Dirección de ordenamiento (asc o desc).
     * @param array $filters Filtros adicionales (key => value).
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $orderBy = null,
        public readonly string $orderDir = 'asc',
        public readonly array $filters = []
    ) {}

    public static function fromRequest(array $data): self
    {
        $reserved = ['search', 'page', 'per_page', 'order_by', 'order_dir'];

        $filters = array_filter(
            array_diff_key($data, array_flip($reserved)),
            fn($v) => $v !== null && $v !== ''
        );

        $orderDir = strtolower($data['order_dir'] ?? 'asc');

        return new self(
            search: $data['search'] ?? null,
            page: max(1, (int) ($data['page'] ?? 1)),
            perPage: min(max(1, (int) ($data['per_page'] ?? 15)), 100),
            orderBy: $data['order_by'] ?? null,
            orderDir: in_array($orderDir, ['asc', 'desc']) ? $orderDir : 'asc',
            filters: $filters
        );
    }
}