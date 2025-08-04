<?php

namespace App\Repositories;

use Illuminate\Http\UploadedFile;
use App\Services\ProcessedRecordService;

class ProcessedRecordRepository
{
    protected ProcessedRecordService $service;

    public function __construct(ProcessedRecordService $service)
    {
        $this->service = $service;
    }

    public function processAndStore(UploadedFile $file): array
    {
        return $this->service->processUploadedFile($file);
    }
}
