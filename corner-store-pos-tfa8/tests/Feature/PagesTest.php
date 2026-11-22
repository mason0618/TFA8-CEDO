<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Concerns\SignsInForManagement;

class PagesTest extends TestCase
{
    use RefreshDatabase, SignsInForManagement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signInForManagement();
    }

    public function test_all_four_pages_have_working_navigation(): void
    {
        $this->seed();

        $pages = [
            '/' => 'Home',
            '/about' => 'About',
            '/customers' => 'Customer Accounts',
            '/users' => 'User Accounts',
        ];

        foreach ($pages as $path => $title) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee('<title>'.$title.' | Corner Store POS</title>', false);

            foreach (array_keys($pages) as $destination) {
                $response->assertSee('href="'.url($destination).'"', false);
            }
        }
    }

    public function test_customer_list_displays_saved_records_and_database_changes(): void
    {
        $this->seed();

        $response = $this->get('/customers');
        $response->assertOk()
            ->assertSeeInOrder(['Ana Reyes', 'Miguel Santos', 'Bea Cruz', 'Paolo Garcia', 'Nina Flores'])
            ->assertSee('ana.reyes@example.com')
            ->assertSee('0917 000 1001');
        $this->assertSame(5, substr_count($response->getContent(), '<th scope="row">'));

        $customer = Customer::factory()->create([
            'full_name' => 'Dina Mercado',
            'email' => 'dina@example.com',
            'phone' => '0918 000 2001',
        ]);

        $this->get('/customers')->assertOk()->assertSee('Dina Mercado')->assertSee('6 records');

        $customer->update(['full_name' => 'Dina Mercado Tan', 'phone' => '0918 000 2002']);

        $this->get('/customers')->assertOk()->assertSee('Dina Mercado Tan')->assertSee('0918 000 2002')->assertDontSee('0918 000 2001');
    }

    public function test_user_list_displays_saved_records_and_database_changes(): void
    {
        $this->seed();

        $response = $this->get('/users');
        $response->assertOk()
            ->assertSeeInOrder(['Marco Dela Cruz', 'Jessa Ramos', 'Carlo Mendoza', 'Lara Aquino', 'Rico Navarro'])
            ->assertSee('marco.dc')
            ->assertSee('rico.n');
        $this->assertSame(5, substr_count($response->getContent(), '<th scope="row">'));

        $user = User::factory()->create(['username' => 'ella.t', 'full_name' => 'Ella Torres']);
        $this->get('/users')->assertOk()->assertSee('Ella Torres')->assertSee('ella.t')->assertSee('6 records');

        $user->update(['username' => 'ella.torres']);
        $this->get('/users')->assertOk()->assertSee('ella.torres')->assertDontSee('<td>ella.t</td>', false);
    }

    public function test_empty_tables_display_empty_messages(): void
    {
        $this->get('/customers')->assertOk()->assertSee('0 records')->assertSee('No customer accounts found.');
        $this->get('/users')->assertOk()->assertSee('0 records')->assertSee('No user accounts found.');
    }

    public function test_saved_names_are_escaped_in_the_views(): void
    {
        $name = '<script>alert("test")</script>';
        Customer::factory()->create(['full_name' => $name]);
        User::factory()->create(['full_name' => $name]);

        foreach (['/customers', '/users'] as $path) {
            $this->get($path)->assertOk()->assertSee(e($name), false)->assertDontSee($name, false);
        }
    }
}
