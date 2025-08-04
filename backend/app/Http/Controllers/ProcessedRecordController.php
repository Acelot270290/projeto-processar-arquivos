<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessedRecordRequest;
use App\Repositories\ProcessedRecordRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class ProcessedRecordController extends Controller
{
    protected ProcessedRecordRepository $repository;

    public function __construct(ProcessedRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Upload e processamento de arquivo
     *
     * @OA\Post(
     *     path="/api/processed-records",
     *     summary="Faz upload de um arquivo e salva os registros processados",
     *     tags={"Processed Records"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     description="Arquivo a ser processado",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Arquivo processado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */


public function store(StoreProcessedRecordRequest $request): JsonResponse
{
    try {
        $file = $request->file('file');

        if (!$file) {
            return response()->json(['error' => 'Nenhum arquivo enviado.'], 400);
        }

        Log::info('📂 Upload recebido', [
            'originalName' => $file->getClientOriginalName(),
            'mimeType' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        $dados = $this->repository->processAndStore($file);

        return response()->json([
            'message' => 'Arquivo processado com sucesso',
            'data' => $dados,
        ]);
    } catch (\Throwable $e) {
        Log::error('❌ Erro ao processar arquivo:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Erro interno ao processar o arquivo',
            'exception' => $e->getMessage(), // opcional, remova em produção
        ], 500);
    }
}

}
