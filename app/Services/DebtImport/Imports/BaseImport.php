<?php

namespace App\Services\DebtImport\Imports;

use App\Helpers\Sanitizer;
use App\Services\OrganizationService;
use Illuminate\Support\Facades\File;

class BaseImport
{
    protected OrganizationService $organizationService;

    protected string $filename;
    protected string $pathFromIntegrations;
    protected string $pathFromManual;
    protected array $additionalData;

    public function __construct()
    {
        $this->organizationService = new OrganizationService(new Sanitizer());
        $this->pathFromIntegrations = 'public/objects-debts/';
        $this->pathFromManual = 'public/objects-debts-manuals/';
        $this->additionalData = [];
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    protected function getAutoPath($isAbsolute = true): string
    {
        if ($isAbsolute) {
            return storage_path() . '/app/public/' . $this->pathFromIntegrations . $this->filename;
        }

        return $this->pathFromIntegrations . $this->filename;
    }

    protected function getManualPath($isAbsolute = true): string
    {
        if ($isAbsolute) {
            return storage_path() . '/app/public/' . $this->pathFromManual . $this->filename;
        }

        return $this->pathFromManual . $this->filename;
    }

    protected function fileToImportNotExist(): bool
    {
        return ! File::exists($this->getAutoPath()) && ! File::exists($this->getManualPath());
    }

    public function getLatestFileToImport(): string
    {
        $autoPath = $this->getAutoPath();
        $manualPath = $this->getManualPath();

        if (! File::exists($autoPath)) {
            return $manualPath;
        }

        if (File::exists($manualPath) && File::lastModified($manualPath) > File::lastModified($autoPath)) {
            return $manualPath;
        }

        return $autoPath;
    }

    public function getSource(): string
    {
        $autoPath = $this->getAutoPath();
        $manualPath = $this->getManualPath();

        if (! File::exists($autoPath)) {
            return $this->getManualPath(false);
        }

        if (File::exists($manualPath) && File::lastModified($manualPath) > File::lastModified($autoPath)) {
            return $this->getManualPath(false);
        }

        return $this->getAutoPath(false);
    }

    public function import(): array
    {
        return [
            'errors' => [],
            'data' => [],
        ];
    }
}