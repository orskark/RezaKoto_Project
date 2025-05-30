<?php

namespace App\Http\Controllers;

use App\DTOs\FilterDTO;
use App\Http\Requests\CreateDocumentTypeRequest;
use App\Http\Requests\UpdateDocumentTypeRequest;
use App\Http\Resources\DocumentTypeResource;
use App\Models\DocumentType;
use App\Services\PaginateRegistersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DocumentTypeController extends Controller
{
    use ApiResponseTrait;
    protected array $searchables = ['name'];

    public function __construct(
        protected PaginateRegistersService $pagination
    ) {}
    /**
     * Muestra todos los tipos de documento.
     */
    public function index(Request $request):JsonResponse
    {
        try {
            $dto = FilterDTO::fromRequest($request->all());
            $results = $this->pagination->execute(DocumentType::query(), $dto, $this->searchables);
            $data = [
                'items' => DocumentTypeResource::collection($results),
                'pagination' => ['current_page' => $results->currentPage(), 'per_page' => $results->perPage(), 'total' => $results->total(), 'last_page' => $results->lastPage()],
            ];
            return $this->successResponse($data,'Registros Obtenidos Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al obtener los registros',500, $e->getMessage());
        }
        
    }

    /**
     * Guarda un nuevo tipo de documento en la base de datos.
     */
    public function store(CreateDocumentTypeRequest $request):JsonResponse
    { 
        try {
            $model = DocumentType::create($request->validated());
            return $this->successResponse(new DocumentTypeResource($model),'Registro Creado Exitosamente',201);
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al crear el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al crear el registro',500, $e->getMessage());
        }
            
    }

    /**
     * Muestra un tipo de documento específico.
     */
    public function show(DocumentType $document_type):JsonResponse
    {
        try {
            return $this->successResponse(new DocumentTypeResource($document_type),'Registro Obtenido Exitosamente');
        } catch (Throwable $e) {
            return $this->errorResponse('Error al mostrar el registro',500,$e->getMessage());
        }
    }

    /**
     * Actualiza un tipo de documento existente.
     */
    public function update(UpdateDocumentTypeRequest $request, DocumentType $document_type):JsonResponse
    {
        try {
            $document_type->update($request->validated());
            return $this->successResponse(new DocumentTypeResource($document_type),'Registro actualizado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al actualizar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al actualizar el registro',500, $e->getMessage());
        }
    }

    /**
     * Elimina un tipo de documento.
     */
    public function destroy(DocumentType $document_type):JsonResponse
    {
        try {
            $document_type->delete();
            return $this->successResponse(null,'Registro eliminado exitosamente');
        } catch (QueryException $e) {
            return $this->errorResponse('Error de base de datos al eliminar el registro',500, $e->getMessage());
        } catch (Throwable $e) {
            return $this->errorResponse('Error al eliminar el registro',500, $e->getMessage());
        }
    }
}

