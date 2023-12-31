<?php

namespace App\MediaSaver;

use Illuminate\Http\UploadedFile;

abstract class MediaManagerContract
{
    public function saveTo(UploadedFile $file, string $folder, array|string $options)
    {
        $path = $file->store($folder, $options);

        $this->verifyPathFilled($path);

        return $path;
    }

    protected function verifyPathFilled(?string $path)
    {
        if ($path) {
            return;
        }

        //
    }
}
