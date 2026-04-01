<?php

namespace App\Service\FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class DummyFileUploader implements FileUploaderInterface
{
    public function upload(UploadedFile $file): string
    {
        dump(sprintf('Dummy file %s uploaded', $file->getBasename()));

        return 'Dummy File';
    }

    public function remove(string $fileName): void
    {
        dump('Dummy file removed');
    }
}
