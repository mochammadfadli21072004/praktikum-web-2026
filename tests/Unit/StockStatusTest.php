<?php

namespace Tests\Unit;

use App\Enums\StockStatus;
use PHPUnit\Framework\TestCase;

class StockStatusTest extends TestCase
{
    public function test_stok_nol_atau_negatif_adalah_habis(): void
    {
        $this->assertSame(StockStatus::Habis, StockStatus::fromQuantity(0));
        $this->assertSame(StockStatus::Habis, StockStatus::fromQuantity(-3));
    }

    public function test_batas_menipis_adalah_1_sampai_10(): void
    {
        $this->assertSame(StockStatus::Menipis, StockStatus::fromQuantity(1));
        $this->assertSame(StockStatus::Menipis, StockStatus::fromQuantity(StockStatus::LOW_STOCK_THRESHOLD));
    }

    public function test_di_atas_batas_adalah_aman(): void
    {
        $this->assertSame(StockStatus::Aman, StockStatus::fromQuantity(11));
        $this->assertSame(StockStatus::Aman, StockStatus::fromQuantity(500));
    }

    public function test_label_dan_warna_sesuai_status(): void
    {
        $this->assertEquals('Habis', StockStatus::Habis->label());
        $this->assertEquals('text-bg-danger', StockStatus::Habis->badgeClass());
        $this->assertEquals('text-bg-warning', StockStatus::Menipis->badgeClass());
        $this->assertEquals('text-bg-success', StockStatus::Aman->badgeClass());
    }
}
