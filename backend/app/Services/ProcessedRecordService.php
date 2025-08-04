<?php

namespace App\Services;

use App\Models\ProcessedRecord;
use Illuminate\Support\Collection;
use SimpleXMLElement;

class ProcessedRecordService
{
    /**
     * Processa o arquivo enviado, detecta tipo, converte e salva registros.
     */
    public function processUploadedFile($uploadedFile): array
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $content = file_get_contents($uploadedFile->getRealPath());
        // dd($content);

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
        $nomeArquivo = $uploadedFile->getClientOriginalName();

        foreach ($normalized as $registro) {
            $registro['nome_arquivo'] = $nomeArquivo; // <-- adiciona ao array antes de salvar
            ProcessedRecord::create($registro);
        }

        return $normalized;
    }

    protected function processJson(string $content): array
    {
        return json_decode($content, true);
    }

    protected function processCsv(string $content): array
    {
        $lines = explode(PHP_EOL, trim($content));
        if (empty($lines)) return [];

        // Detectar delimitador
        $firstLine = $lines[0];
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        $headers = str_getcsv(array_shift($lines), $delimiter);
        $data = [];

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            $values = str_getcsv($line, $delimiter);
            if (count($values) !== count($headers)) continue;

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
                'metrica_a'     => $parts[1] ?? null,
                'metrica_b'     => $parts[2] ?? null,
                'indicador_x'   => $parts[3] ?? null,
                'indicador_y'   => $parts[4] ?? null,
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
                'metrica_a'     => (string) $registro->metrica_a,
                'metrica_b'     => (string) $registro->metrica_b,
                'indicador_x'   => (string) $registro->indicador_x,
                'indicador_y'   => (string) $registro->indicador_y,
            ];
        }

        return $data;
    }

    protected function normalizeData(array $rawData): array
    {
        return collect($rawData)->map(function ($item) {
            // Detecta valores numéricos para cada métrica
            $metrica_a = $this->parseNumber($item['metrica_a'] ?? $item['idade'] ?? null);
            $metrica_b = $this->parseNumber($item['metrica_b'] ?? null);
            $indicador_x = $this->parseNumber($item['indicador_x'] ?? null);
            $indicador_y = $this->parseNumber($item['indicador_y'] ?? null);

            // Define os campos que foram usados como métricas
            $camposUsados = [
                'data_registro',
                'metrica_a',
                'metrica_b',
                'indicador_x',
                'indicador_y',
                'idade'
            ];

            $data = [
                'data_registro' => $item['data_registro'] ?? now()->toDateString(),
                'metrica_a'     => $metrica_a,
                'metrica_b'     => $metrica_b,
                'indicador_x'   => $indicador_x,
                'indicador_y'   => $indicador_y,
            ];

            // O restante vai para o campo extra
            $extra = collect($item)->except($camposUsados)->toArray();
            $data['extra'] = !empty($extra) ? json_encode($extra) : null;

            return $data;
        })->all();
    }




    protected function parseNumber($value): ?float
    {
        if (is_null($value)) return null;

        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        return is_numeric($cleaned) ? floatval($cleaned) : null;
    }
}
