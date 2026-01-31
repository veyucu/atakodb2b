<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Dosya depolama işlemleri için ortak metodlar
 */
trait ManagesFileStorage
{
    /**
     * Storage'dan dosya sil (URL değilse)
     */
    protected function deleteStorageFile(?string $path, string $disk = 'public'): bool
    {
        if (empty($path)) {
            return false;
        }

        // URL ise silme (harici resimler)
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
            return true;
        }

        return false;
    }

    /**
     * Dosya yükle ve eski dosyayı sil
     */
    protected function uploadAndReplaceFile(
        UploadedFile $file,
        string $directory,
        ?string $oldPath = null,
        string $disk = 'public'
    ): string {
        // Eski dosyayı sil
        if ($oldPath) {
            $this->deleteStorageFile($oldPath, $disk);
        }

        // Yeni dosyayı yükle
        return $file->store($directory, $disk);
    }

    /**
     * Model'deki resim alanını güncelle (varsa eski resmi sil)
     */
    protected function updateModelImage(
        $model,
        ?UploadedFile $file,
        string $attribute = 'image',
        string $directory = 'images',
        string $disk = 'public'
    ): void {
        if (!$file) {
            return;
        }

        $oldPath = $model->{$attribute};
        $newPath = $this->uploadAndReplaceFile($file, $directory, $oldPath, $disk);
        $model->{$attribute} = $newPath;
    }
}
