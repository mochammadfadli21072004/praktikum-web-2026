<?php

namespace Tests\Unit;

use App\Enums\TaskPriority;
use PHPUnit\Framework\TestCase;

class TaskPriorityTest extends TestCase
{
    public function test_label_setiap_prioritas(): void
    {
        $this->assertSame('Rendah', TaskPriority::Low->label());
        $this->assertSame('Sedang', TaskPriority::Medium->label());
        $this->assertSame('Tinggi', TaskPriority::High->label());
    }

    public function test_nilai_enum_sesuai_kolom_database(): void
    {
        $this->assertSame(['low', 'medium', 'high'], array_column(TaskPriority::cases(), 'value'));
    }

    public function test_nilai_di_luar_daftar_tidak_valid(): void
    {
        $this->assertNull(TaskPriority::tryFrom('urgent'));
    }
}
