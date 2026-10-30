<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Semua urusan file gambar produk.
 * (Refactor P9: Extract Class/Service — sebelumnya logika ini diulang di store, update, dan destroy.)
 */
class ProductImageService
{
    private const DISK = 'public';
    private const DIRECTORY = 'products';

    public function store(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::DISK);
    }

    /** Hapus gambar lama (jika ada), simpan yang baru. */
    public function replace(?string $oldPath, UploadedFile $file): string
    {
        $this->delete($oldPath);

        return $this->store($file);
    }

    public function delete(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }
}
