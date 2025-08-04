<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessedRecordRequest;
use App\Repositories\ProcessedRecordRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Models\ProcessedRecord;
use Illuminate\Support\Facades\DB;

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
        $file = $request->file('file');
        $dados = $this->repository->processAndStore($file);

        return response()->json([
            'message' => 'Arquivo processado com sucesso',
            'data' => $dados,
        ]);
    }

    /**
     * Lista os nomes de arquivos já processados
     *
     * @OA\Get(
     *     path="/api/files",
     *     summary="Retorna os nomes dos arquivos processados",
     *     tags={"Processed Records"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de nomes de arquivos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="exemplo_dados.csv")
     *         )
     *     )
     * )
     */
    public function listarArquivos(): JsonResponse
    {
        $nomes = ProcessedRecord::whereNotNull('nome_arquivo')
            ->distinct()
            ->orderBy('nome_arquivo')
            ->pluck('nome_arquivo');

        return response()->json($nomes);
    }

    /**
     * Retorna os registros processados de um arquivo específico
     *
     * @OA\Get(
     *     path="/api/files/{nome}",
     *     summary="Retorna os registros processados de um arquivo",
     *     tags={"Processed Records"},
     *     @OA\Parameter(
     *         name="nome",
     *         in="path",
     *         description="Nome do arquivo",
     *         required=true,
     *         @OA\Schema(type="string", example="exemplo_dados.csv")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registros do arquivo",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="data_registro", type="string", format="date"),
     *                 @OA\Property(property="metrica_a", type="number", format="float"),
     *                 @OA\Property(property="metrica_b", type="number", format="float"),
     *                 @OA\Property(property="indicador_x", type="number", format="float"),
     *                 @OA\Property(property="indicador_y", type="number", format="float"),
     *                 @OA\Property(property="nome_arquivo", type="string")
     *             )
     *         )
     *     )
     * )
     */

    public function mostrarArquivo(string $nome): JsonResponse
    {
        $registros = ProcessedRecord::where('nome_arquivo', $nome)->get();

        return response()->json($registros);
    }
}
