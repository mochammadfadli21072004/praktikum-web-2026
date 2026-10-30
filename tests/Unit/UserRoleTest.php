<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_admin_dikenali_sebagai_admin(): void
    {
        $user = new User();
        $user->role = User::ROLE_ADMIN;

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isKasir());
    }

    public function test_kasir_bukan_admin(): void
    {
        $user = new User();
        $user->role = User::ROLE_KASIR;

        $this->assertTrue($user->isKasir());
        $this->assertFalse($user->isAdmin());
    }
}
