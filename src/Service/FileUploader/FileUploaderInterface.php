<?php

namespace App\Service\FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploaderInterface
{
    /**
     * @return string Chemin du fichier uploadé
     */
    public function upload(UploadedFile $file): string;
    public function remove(string $fileName): void;
}
