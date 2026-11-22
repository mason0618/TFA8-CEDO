<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_public_and_authentication_pages(): void
    {
        foreach (['/', '/about', '/login', '/register', '/forgot-password'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_every_resource_action_redirects_guests_without_changing_records(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();

        foreach (['customers' => $customer->id, 'users' => $user->id] as $resource => $id) {
            foreach (['/'.$resource, '/'.$resource.'/create', '/'.$resource.'/'.$id, '/'.$resource.'/'.$id.'/edit'] as $path) {
                $this->get($path)->assertRedirect(route('login'));
            }

            $this->post('/'.$resource, [])->assertRedirect(route('login'));
            $this->put('/'.$resource.'/'.$id, [])->assertRedirect(route('login'));
            $this->delete('/'.$resource.'/'.$id)->assertRedirect(route('login'));
        }

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_dashboard_profile_and_avatar_routes_require_authentication(): void
    {
        foreach (['/dashboard', '/profile', '/storage/avatars/'.Str::uuid().'.jpg'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_the_same_account_pages_are_available_after_signing_in(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $this->actingAs($user);

        foreach (['/dashboard', '/customers', '/customers/create', '/customers/'.$customer->id, '/customers/'.$customer->id.'/edit', '/users', '/users/create', '/users/'.$user->id, '/users/'.$user->id.'/edit', '/profile'] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_authenticated_users_can_create_update_show_and_delete_customers(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post('/customers', ['full_name' => 'Resource Customer', 'email' => 'resource@example.com'])->assertRedirect('/customers');
        $customer = Customer::where('email', 'resource@example.com')->firstOrFail();
        $this->get('/customers/'.$customer->id)->assertOk()->assertSee('Resource Customer');
        $this->put('/customers/'.$customer->id, ['full_name' => 'Updated Customer', 'email' => 'updated@example.com'])->assertRedirect('/customers');
        $this->assertSame('Updated Customer', $customer->fresh()->full_name);
        $this->delete('/customers/'.$customer->id)->assertRedirect('/customers');
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_authenticated_users_can_manage_another_user_and_its_avatar(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $this->post('/users', [
            'username' => 'resource.user', 'full_name' => 'Resource User', 'email' => 'resource@example.com',
            'password' => 'NewAccount!2026', 'password_confirmation' => 'NewAccount!2026',
        ])->assertRedirect('/users');
        $user = User::where('username', 'resource.user')->firstOrFail();
        $this->assertTrue(Hash::check('NewAccount!2026', $user->password));
        $this->get('/users/'.$user->id)->assertOk()->assertSee('Resource User');
        $this->put('/users/'.$user->id, ['username' => 'resource.user', 'full_name' => 'Updated User', 'email' => $user->email])->assertRedirect('/users');
        $this->assertSame('Updated User', $user->fresh()->full_name);

        $path = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, 'test avatar');
        $user->update(['avatar' => $path]);
        $this->delete('/users/'.$user->id)->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_self_deletion_requires_the_profile_password_confirmation_flow(): void
    {
        Storage::fake('public');
        $path = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, 'test avatar');
        $user = User::factory()->create(['avatar' => $path]);
        $this->actingAs($user);

        $this->delete('/users/'.$user->id)->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->delete('/profile', ['password' => 'password'])->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull($user->fresh());
        Storage::disk('public')->assertMissing($path);
    }

    public function test_password_hashes_and_remember_tokens_are_not_exposed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/users')->assertOk()->assertDontSee($user->password, false);
        $this->get('/users/'.$user->id)->assertOk()->assertDontSee($user->password, false);
        $this->assertArrayNotHasKey('password', $user->toArray());
        $this->assertArrayNotHasKey('remember_token', $user->toArray());
    }

    public function test_registration_saves_pos_fields_and_hashes_the_password(): void
    {
        $this->post('/register', [
            'full_name' => 'Registered Person', 'username' => 'registered.person', 'email' => 'registered@example.com',
            'password' => 'Register!2026', 'password_confirmation' => 'Register!2026',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'registered@example.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('Registered Person', $user->full_name);
        $this->assertSame('registered.person', $user->username);
        $this->assertTrue(Hash::check('Register!2026', $user->password));
        $this->assertNotSame('Register!2026', $user->password);
    }

    public function test_invalid_registration_cannot_create_accounts_or_flash_passwords(): void
    {
        User::factory()->create(['username' => 'taken.user', 'email' => 'taken@example.com']);
        $this->from('/register')->post('/register', [
            'full_name' => 'Trying Registration', 'username' => 'taken.user', 'email' => 'taken@example.com',
            'password' => 'short', 'password_confirmation' => 'different',
        ])->assertRedirect('/register')->assertSessionHasErrors(['username', 'email', 'password'])
            ->assertSessionMissing('_old_input.password')->assertSessionMissing('_old_input.password_confirmation');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_failed_logins_are_throttled_by_breezes_default_limiter(): void
    {
        $user = User::factory()->create();
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        }
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_existing_passwords_cannot_be_changed_through_a_resource_update(): void
    {
        $user = User::factory()->create();
        $original = $user->password;
        $this->actingAs($user)->put('/users/'.$user->id, [
            'username' => $user->username, 'full_name' => $user->full_name, 'email' => $user->email,
            'password' => 'Changed!2026', 'password_confirmation' => 'Changed!2026',
        ])->assertSessionHasErrors('password');
        $this->assertSame($original, $user->fresh()->password);
    }

    public function test_a_legacy_directory_user_can_be_given_login_credentials(): void
    {
        $manager = User::factory()->create();
        $legacy = User::create(['username' => 'legacy.user', 'full_name' => 'Legacy User']);
        $this->actingAs($manager)->put('/users/'.$legacy->id, [
            'username' => 'legacy.user', 'full_name' => 'Legacy User', 'email' => 'legacy@example.com',
            'password' => 'LegacyAccount!2026', 'password_confirmation' => 'LegacyAccount!2026',
        ])->assertRedirect('/users');
        $this->assertTrue(Hash::check('LegacyAccount!2026', $legacy->fresh()->password));
    }

    public function test_login_and_logout_renew_the_session(): void
    {
        $user = User::factory()->create();
        $this->get('/login')->assertOk();
        $beforeLogin = session()->getId();

        $this->withCookie(config('session.cookie'), $beforeLogin)->post('/login', [
            'email' => $user->email, 'password' => 'password',
        ])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($beforeLogin, session()->getId());

        $beforeLogout = session()->getId();
        $token = session()->token();
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->assertNotSame($beforeLogout, session()->getId());
        $this->assertNotSame($token, session()->token());
    }
}
