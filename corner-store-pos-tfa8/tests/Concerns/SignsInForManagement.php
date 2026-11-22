<?php

namespace Tests\Concerns;

use App\Models\User;

trait SignsInForManagement
{
    protected function signInForManagement(): void
    {
        // Keep the original directory tests independent of extra account rows.
        $this->actingAs(User::factory()->make([
            'id' => 999999,
            'full_name' => 'Test Operator',
            'username' => 'test.operator',
            'email' => 'operator@example.com',
        ]));
    }
}
