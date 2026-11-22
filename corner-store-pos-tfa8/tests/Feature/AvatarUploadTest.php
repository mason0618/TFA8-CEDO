<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Concerns\SignsInForManagement;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase, SignsInForManagement;

    protected function setUp(): void
    {
        parent::setUp();
        $this->signInForManagement();
        Storage::fake('public');
    }

    private function updateUser(User $user, array $extra = [])
    {
        return $this->from('/users/'.$user->id.'/edit')->put('/users/'.$user->id, array_merge([
            'username' => $user->username,
            'full_name' => $user->full_name,
            'email' => $user->email,
        ], $extra));
    }

    public function test_a_real_image_is_prepared_as_a_256_pixel_jpeg_and_listed(): void
    {
        $user = User::factory()->create();
        $this->updateUser($user, ['avatar' => UploadedFile::fake()->image('original-name.png', 800, 500)])
            ->assertRedirect('/users')->assertSessionHasNoErrors();

        $path = $user->fresh()->avatar;
        $this->assertMatchesRegularExpression('~^avatars/[a-f0-9-]{36}\.jpg$~', $path);
        Storage::disk('public')->assertExists($path);
        $size = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(256, $size[0]);
        $this->assertSame(256, $size[1]);
        $this->assertSame('image/jpeg', $size['mime']);
        $this->assertCount(1, Storage::disk('public')->allFiles());
        $this->get('/users')->assertOk()->assertSee('storage/'.$path)->assertSee('width="48" height="48"', false);
        $this->get('/storage/'.$path)->assertOk()->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_replacing_an_avatar_removes_the_old_file_after_saving(): void
    {
        $old = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($old, 'old image');
        $user = User::factory()->create(['avatar' => $old]);
        $this->updateUser($user, ['avatar' => UploadedFile::fake()->image('new.jpg', 400, 600)])->assertRedirect('/users')->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_an_edit_without_a_file_preserves_the_current_avatar(): void
    {
        $old = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($old, 'old image');
        $user = User::factory()->create(['avatar' => $old]);
        $this->updateUser($user, ['full_name' => 'Updated User'])->assertRedirect('/users');
        $this->assertSame($old, $user->fresh()->avatar);
        Storage::disk('public')->assertExists($old);
    }

    public function test_non_images_and_client_supplied_paths_are_rejected(): void
    {
        $user = User::factory()->create(['full_name' => 'Saved User']);
        $file = UploadedFile::fake()->createWithContent('script.php', '<?php echo "bad";');
        $this->updateUser($user, ['avatar' => $file, 'full_name' => 'Unsaved User'])->assertSessionHasErrors('avatar');
        $this->updateUser($user, ['avatar' => 'avatars/supplied-path.jpg'])->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
        $this->assertSame('Saved User', $user->fresh()->full_name);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_images_larger_than_2_mb_are_rejected_without_changes(): void
    {
        $user = User::factory()->create();
        $this->updateUser($user, ['avatar' => UploadedFile::fake()->image('large.jpg')->size(2049)])->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_an_image_at_the_2_mb_limit_is_accepted(): void
    {
        $user = User::factory()->create();
        $this->updateUser($user, ['avatar' => UploadedFile::fake()->image('limit.png', 300, 300)->size(2048)])->assertRedirect('/users')->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_corrupt_images_and_svg_files_are_rejected(): void
    {
        $user = User::factory()->create();
        $corrupt = UploadedFile::fake()->createWithContent('broken.png', "\x89PNG\r\n\x1a\ninvalid data");
        $svg = UploadedFile::fake()->createWithContent('vector.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->updateUser($user, ['avatar' => $corrupt])->assertSessionHasErrors('avatar');
        $this->updateUser($user, ['avatar' => $svg])->assertSessionHasErrors('avatar');
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_users_without_an_avatar_have_a_placeholder_and_missing_files_are_rejected(): void
    {
        User::factory()->create();
        $this->get('/users')->assertOk()->assertSee('images/avatar-placeholder.svg');
        $this->get('/storage/avatars/'.Str::uuid().'.jpg')->assertNotFound();
        $this->get('/storage/avatars/not-an-avatar.php')->assertForbidden();
    }

    public function test_invalid_edits_preserve_the_existing_file_and_old_text_input(): void
    {
        $path = 'avatars/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, 'existing image');
        $user = User::factory()->create(['avatar' => $path]);
        $this->updateUser($user, ['full_name' => 'Typed Name', 'avatar' => UploadedFile::fake()->create('bad.txt')])
            ->assertSessionHasErrors('avatar')->assertSessionHasInput('full_name', 'Typed Name');
        $this->withCookie(config('session.cookie'), session()->getId())->get('/users/'.$user->id.'/edit')->assertOk()->assertSee('value="Typed Name"', false);
        $this->assertSame($path, $user->fresh()->avatar);
        Storage::disk('public')->assertExists($path);
    }

    public function test_a_failed_database_update_cleans_up_the_new_upload(): void
    {
        $user = User::factory()->create();
        User::updating(function () { throw new \RuntimeException('Simulated database failure'); });

        try {
            $this->updateUser($user, ['avatar' => UploadedFile::fake()->image('new.jpg', 300, 400)])->assertStatus(500);
            $this->assertSame([], Storage::disk('public')->allFiles());
            $this->assertNull($user->fresh()->avatar);
        } finally {
            User::flushEventListeners();
        }
    }
}
