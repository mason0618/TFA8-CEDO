<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Concerns\SignsInForManagement;

class CustomerFormsTest extends TestCase
{
    use RefreshDatabase, SignsInForManagement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signInForManagement();
    }

    public function test_create_and_edit_forms_include_csrf_and_existing_values(): void
    {
        $customer = Customer::factory()->create(['full_name' => 'Existing Customer', 'email' => 'old@example.com']);
        $this->get('/customers/create')->assertOk()->assertSee('name="_token"', false)->assertSee('Create Customer');
        $this->get('/customers/'.$customer->id.'/edit')->assertOk()->assertSee('value="Existing Customer"', false)->assertSee('value="old@example.com"', false)->assertSee('name="_method" value="PUT"', false);
    }

    public function test_a_valid_customer_can_be_created_with_optional_phone(): void
    {
        $this->post('/customers', ['full_name' => 'Dina Mercado', 'email' => 'dina@example.com'])
            ->assertRedirect('/customers')->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['full_name' => 'Dina Mercado', 'email' => 'dina@example.com', 'phone' => '']);
    }

    public function test_invalid_customer_fields_redisplay_errors_and_old_input(): void
    {
        $this->from('/customers/create')->post('/customers', ['full_name' => 'Trying Customer', 'email' => 'not-an-email', 'phone' => '0917 123 4567'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors('email')->assertSessionHasInput('full_name', 'Trying Customer');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/customers/create')->assertOk()->assertSee('value="Trying Customer"', false)->assertSee('value="not-an-email"', false)->assertSee('value="0917 123 4567"', false)->assertSee('Please correct the following:');
        $this->assertDatabaseCount('customers', 0);

        $this->post('/customers', ['full_name' => '', 'email' => ''])->assertSessionHasErrors(['full_name', 'email']);
    }

    public function test_an_existing_customer_can_be_updated(): void
    {
        $customer = Customer::factory()->create();
        $this->put('/customers/'.$customer->id, ['full_name' => 'Changed Customer', 'email' => 'changed@example.com', 'phone' => '0917 100 2000'])
            ->assertRedirect('/customers')->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Changed Customer', 'email' => 'changed@example.com', 'phone' => '0917 100 2000']);
        $this->assertDatabaseCount('customers', 1);
    }

    public function test_invalid_edits_do_not_change_saved_customer_data(): void
    {
        $customer = Customer::factory()->create(['full_name' => 'Saved Customer', 'email' => 'saved@example.com']);
        $editUrl = '/customers/'.$customer->id.'/edit';
        $this->from($editUrl)->put('/customers/'.$customer->id, ['full_name' => 'Unsaved Customer', 'email' => 'bad-email'])
            ->assertRedirect($editUrl)->assertSessionHasErrors('email');
        $this->withCookie(config('session.cookie'), session()->getId())->get($editUrl)->assertOk()->assertSee('value="Unsaved Customer"', false)->assertSee('value="bad-email"', false);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Saved Customer', 'email' => 'saved@example.com']);
    }

    public function test_missing_customer_ids_return_404(): void
    {
        $this->get('/customers/999999/edit')->assertNotFound();
        $this->put('/customers/999999', ['full_name' => 'Nobody', 'email' => 'nobody@example.com'])->assertNotFound();
    }
}
