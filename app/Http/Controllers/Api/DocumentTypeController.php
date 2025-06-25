<?php

namespace App\Http\Controllers\Api;

use App\DTOs\FilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Http\Resources\DocumentTypeResource;
use App\Models\DocumentType;
use App\Services\PaginateRegistersService;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class DocumentTypeController extends Controller
{
    use ApiResponseTrait;

    protected array $searchables = ['name'];
    protected array $filterables = ['status_id'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $query = DocumentType::query();
            $results = $this->pagination->execute($query, $dto, $this->searchables, $this->filterables);
            $data = [
                'items' => DocumentTypeResource::collection($results),
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'last_page' => $results->lastPage(),
                ],
            ];
            return $this->successResponse($data, 'Registros obtenidos exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros.', 500, $e->getMessage());
        }
    }

    public function store(CreateDocumentTypeRequest $request): JsonResponse
    {
        try {
            $document_type = DocumentType::create($request->validated());
            return $this->successResponse(new DocumentTypeResource($document_type), 'Registro creado exitosamente.', 201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error al crear el registro.', 500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Ha ocurrido un error inesperado.', 500, $e->getMessage());
        }
    }

    public function show(DocumentType $document_type): JsonResponse
    {
        try {
            $resource = new DocumentTypeResource($document_type);
            return $this->successResponse($resource, 'Registro obtenido exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener el registro.', 500, $e->getMessage());
        }
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $document_type): JsonResponse
    {
        try {
            $document_type->update($request->validated());
            $resource = new DocumentTypeResource($document_type);
            return $this->successResponse($resource, 'Registro actualizado exitosamente.');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }

    public function destroy(DocumentType $document_type): JsonResponse
    {
        try {
            $document_type->delete();
            return $this->successResponse(null, 'Registro eliminado exitosamente.', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro.', 500, $e->getMessage());
        }
    }

    public function toggleStatus(DocumentType $document_type): JsonResponse
    {
        try {
            $document_type->status_id = $document_type->status_id == 1 ? 2 : 1;
            $document_type->save();
            return $this->successResponse($document_type, 'Registro actualizado éxitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro.', 500, $e->getMessage());
        }
    }
}
