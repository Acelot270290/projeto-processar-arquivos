<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Repositories\ProcessedRecordRepository;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class ProcessedRecordService
{
    protected $repository;

    public function __construct(ProcessedRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Processa o arquivo enviado, detecta tipo, converte e salva registros.
     */
    public function processUploadedFile($uploadedFile): array
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $content = file_get_contents($uploadedFile->getRealPath());

        switch ($extension) {
            case 'json':
                $parsedData = $this->processJson($content);
                break;

            case 'csv':
                $parsedData = $this->processCsv($content);
                break;

            case 'txt':
                $parsedData = $this->processTxt($content);
                break;

            case 'xml':
                $parsedData = $this->processXml($content);
                break;

            default:
                throw new \Exception("Tipo de arquivo não suportado: .$extension");
        }

        $normalized = $this->normalizeData($parsedData);

        foreach ($normalized as $registro) {
            $this->repository->create($registro);
        }

        return $normalized;
    }

    protected function processJson(string $content): array
    {
        return json_decode($content, true);
    }

    protected function processCsv(string $content): array
    {
        $lines = explode(PHP_EOL, $content);
        $headers = str_getcsv(array_shift($lines), ';');
        $data = [];

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            $values = str_getcsv($line, ';');
            $data[] = array_combine($headers, $values);
        }

        return $data;
    }

    protected function processTxt(string $content): array
    {
        $lines = explode(PHP_EOL, $content);
        $data = [];

        foreach ($lines as $line) {
            if (trim($line) === '') continue;
            $parts = preg_split('/\s+/', $line);
            $data[] = [
                'data_registro' => $parts[0] ?? null,
                'metrica_a' => $parts[1] ?? null,
                'metrica_b' => $parts[2] ?? null,
                'indicador_x' => $parts[3] ?? null,
                'indicador_y' => $parts[4] ?? null,
            ];
        }

        return $data;
    }

    protected function processXml(string $content): array
    {
        $xml = new SimpleXMLElement($content);
        $data = [];

        foreach ($xml->registro as $registro) {
            $data[] = [
                'data_registro' => (string) $registro->data_registro,
                'metrica_a' => (string) $registro->metrica_a,
                'metrica_b' => (string) $registro->metrica_b,
                'indicador_x' => (string) $registro->indicador_x,
                'indicador_y' => (string) $registro->indicador_y,
            ];
        }

        return $data;
    }

    protected function normalizeData(array $rawData): array
    {
        return collect($rawData)->map(function ($item) {
            return [
                'data_registro' => $item['data_registro'] ?? null,
                'metrica_a'     => $this->parseNumber($item['metrica_a'] ?? null),
                'metrica_b'     => $this->parseNumber($item['metrica_b'] ?? null),
                'indicador_x'   => $this->parseNumber($item['indicador_x'] ?? null),
                'indicador_y'   => $this->parseNumber($item['indicador_y'] ?? null),
            ];
        })->all();
    }

    protected function parseNumber($value): ?float
    {
        if (is_null($value)) return null;

        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        return is_numeric($cleaned) ? floatval($cleaned) : null;
    }
}
