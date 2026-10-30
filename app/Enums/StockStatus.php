<?php

namespace App\Enums;

/**
 * Status stok produk. Satu-satunya tempat aturan "habis / menipis / aman".
 * (Refactor P9: Replace Magic Value — sebelumnya angka 10 ada di Badge dan angka 5 di API v2.)
 */
enum StockStatus: string
{
    case Habis = 'habis';
    case Menipis = 'menipis';
    case Aman = 'aman';

    /** Stok 1 sampai batas ini dianggap menipis. */
    public const LOW_STOCK_THRESHOLD = 10;

    public static function fromQuantity(int $quantity): self
    {
        return match (true) {
            $quantity <= 0 => self::Habis,
            $quantity <= self::LOW_STOCK_THRESHOLD => self::Menipis,
            default => self::Aman,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Habis => 'Habis',
            self::Menipis => 'Menipis',
            self::Aman => 'Aman',
        };
    }

    /** Kelas warna Bootstrap 5. */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Habis => 'text-bg-danger',
            self::Menipis => 'text-bg-warning',
            self::Aman => 'text-bg-success',
        };
    }
}
