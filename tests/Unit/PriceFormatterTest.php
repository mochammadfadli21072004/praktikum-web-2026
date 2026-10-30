<?php

namespace Tests\Unit;

use App\Support\PriceFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Unit test: logika murni, tanpa database dan tanpa HTTP.
 * extends PHPUnit\Framework\TestCase (bukan Tests\TestCase).
 */
class PriceFormatterTest extends TestCase
{
    public function test_memformat_angka_bulat_menjadi_rupiah(): void
    {
        $this->assertEquals('Rp 15.000', PriceFormatter::rupiah(15000));
        $this->assertEquals('Rp 1.250.000', PriceFormatter::rupiah(1250000));
    }

    public function test_menerima_string_desimal_dari_database(): void
    {
        $this->assertEquals('Rp 125.000', PriceFormatter::rupiah('125000.00'));
    }

    public function test_nol_dan_null_menjadi_rp_0(): void
    {
        $this->assertEquals('Rp 0', PriceFormatter::rupiah(0));
        $this->assertEquals('Rp 0', PriceFormatter::rupiah(null));
    }

    public function test_pecahan_dibulatkan_tanpa_desimal(): void
    {
        $this->assertEquals('Rp 1.000', PriceFormatter::rupiah(999.6));
    }
}
