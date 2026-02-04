<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader
{
    public function __construct(
        private string $targetDir // injecté via services.yaml
    ) {}

    public function upload(UploadedFile $file): string
    {
        $safeName = bin2hex(random_bytes(8));
        $ext = $file->guessExtension() ?: 'bin';

        $filename = $safeName.'.'.$ext;
        $file->move($this->targetDir, $filename);

        // Chemin public stocké en DB
        return 'uploads/photos/'.$filename;
    }
}
