<?php

namespace App\Support;

/**
 * Format rupiah di satu tempat.
 * (Refactor P9: Extract Class — sebelumnya rumus yang sama ditulis di Model Product dan di directive @rupiah.)
 */
final class PriceFormatter
{
    public static function rupiah(int|float|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
