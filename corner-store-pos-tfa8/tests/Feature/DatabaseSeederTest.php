<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Tests\Concerns\SignsInForManagement;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase, SignsInForManagement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signInForManagement();
    }

    public function test_seeders_create_five_accounts_each_without_duplicates(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('customers', [
            'full_name' => 'Ana Reyes',
            'email' => 'ana.reyes@example.com',
            'phone' => '0917 000 1001',
        ]);
        $this->assertDatabaseHas('users', ['username' => 'marco.dc', 'full_name' => 'Marco Dela Cruz']);
    }

    public function test_tables_match_the_required_schema(): void
    {
        $customerColumns = Schema::getColumnListing('customers');
        $userColumns = Schema::getColumnListing('users');
        sort($customerColumns);
        sort($userColumns);

        $this->assertSame(['created_at', 'email', 'full_name', 'id', 'phone', 'updated_at'], $customerColumns);
        $this->assertSame(['avatar', 'created_at', 'email', 'email_verified_at', 'full_name', 'id', 'password', 'remember_token', 'updated_at', 'username'], $userColumns);
    }

    public function test_usernames_must_be_unique_in_the_database(): void
    {
        User::factory()->create(['username' => 'same.username']);

        $this->expectException(QueryException::class);
        User::factory()->create(['username' => 'same.username']);
    }
}
