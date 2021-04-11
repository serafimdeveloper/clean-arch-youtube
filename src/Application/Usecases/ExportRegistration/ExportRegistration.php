<?php

declare(strict_types=1);

namespace App\Application\Usecases\ExportRegistration;

use App\Application\Contracts\ExportRegistrationPdfExporter;
use App\Application\Contracts\UseCaseBoundary;
use App\Application\Contracts\Storage;
use App\Domain\Repositories\LoadRegistrationRepository;
use App\Domain\ValueObjects\Cpf;

final class ExportRegistration
{
    private LoadRegistrationRepository $repository;
    private ExportRegistrationPdfExporter $pdfExporter;
    private Storage $storage;

    public function __construct(
        LoadRegistrationRepository $repository,
        ExportRegistrationPdfExporter $pdfExporter,
        Storage $storage
    ) {
        $this->repository = $repository;
        $this->pdfExporter = $pdfExporter;
        $this->storage = $storage;
    }

    public function handle(InputData $input): UseCaseBoundary
    {
        $cpf = new Cpf($input->registrationNumber);
        $registration = $this->repository->loadByRegistrationNumber($cpf);
        $fileContent = $this->pdfExporter->generate($registration);

        $this->storage->store($input->pdfFileName, $input->path, $fileContent);

        return OutputData::create(['fullFileName' => $input->getFullFileName()]);
    }
}
