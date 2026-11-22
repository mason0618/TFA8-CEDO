# Corner Store POS - TFA8

A continuation of the TFA7 POS application for IT0049 Technical Formative Assessment 8, **Locking It Down**.

Customer and user account management now use complete Laravel resource controllers. Laravel Breeze supplies login, registration, password reset, password confirmation, and profile handling. Account routes require an authenticated session.

## Requirements

- PHP 8.3 or newer with Laravel's required extensions
- Composer 2
- SQLite support: `pdo_sqlite` and `sqlite3`
- PHP `gd` and `exif` extensions for the TFA7 avatar workflow

The project includes **Laravel Breeze 2.4.2** and Intervention Image in its Composer lock file. Breeze's Blade views have been adapted to the existing plain CSS. There is no frontend build step, so Node.js and npm are not needed.

## Fresh installation

Extract this project into its own folder. Open a terminal in the folder containing `artisan` and `composer.json`, then run:

```sh
composer install
composer run setup
php artisan serve
```

Open **http://127.0.0.1:8000**.

Setup copies `.env.example` when needed, generates the application key, creates the SQLite database, and runs migrations and seeders. A fresh installation contains five customers and five users.

### Local demo login

- Email: **marco@example.com**
- Password: **StoreDemo!2026**

The five seeded sample users use this demo password. Passwords are saved as hashes. These credentials are for the local assessment data. You can also use **Register** to create your own account.

Run setup once on a fresh installation. The finished project already contains the customized Breeze scaffold; do not rerun `breeze:install`, which would overwrite the integrated views and routes.

## Updating an existing TFA7 installation

Keep your existing `.env`, database, and uploaded avatars while updating the application source. Keep the existing `APP_KEY`.

Set the cache store in `.env` to:

```dotenv
CACHE_STORE=database
```

If running TFA7 and TFA8 side by side, use a separate cookie name for TFA8:

```dotenv
SESSION_COOKIE=corner_store_pos_tfa8_session
```

Then run:

```sh
composer install
php artisan migrate
php artisan config:clear
php artisan serve
```

The original TFA7 migrations are unchanged. New migrations add login fields, password reset tokens, and database cache tables. Existing names, usernames, customers, and avatar paths are retained.

TFA7 records did not have login credentials. After upgrading, open `/register` to create the first authenticated account. You can then edit imported user accounts to give them a login email and an initial password. Do not reseed a database whose edited sample values you want to preserve.

Do not run `migrate:fresh` on a database whose records you need to keep.

## Resource routing

`routes/web.php` places both resource registrations inside the same authentication group:

```php
Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('users', UserController::class);
});
```

Both controllers implement all seven conventional methods:

| Method | Responsibility |
| --- | --- |
| `index` | List records |
| `create` | Display the create form |
| `store` | Validate and save a new record |
| `show` | Display one record |
| `edit` | Display a prefilled edit form |
| `update` | Validate and save changes |
| `destroy` | Delete a record |

The lists include View, Edit, and Delete controls. Delete forms use CSRF tokens and the DELETE method, with a browser confirmation prompt.

## Public and protected pages

| Access | Pages |
| --- | --- |
| Public | Home `/`, About `/about` |
| Guest authentication | `/login`, `/register`, `/forgot-password`, password reset forms |
| Authenticated | `/dashboard`, every customer and user resource route, `/profile`, avatar display, password confirmation and updates |

A guest visiting an account page is redirected to login. After successful login, Breeze returns the user to the originally requested page. Logging out invalidates the session and removes access to protected pages.

All authenticated users can manage account records, as required by the assessment. Role-based permissions are not part of this activity.

## User credentials and profile settings

Registration and user creation require a full name, unique username, unique login email, and confirmed password. The project keeps TFA7's `full_name` field instead of introducing a second `name` column.

Existing passwords are changed through the account owner's Profile page, where Breeze requires the current password. The account-management edit form allows initial credentials for imported TFA7 records but does not bypass Profile's current-password check for an existing login.

The user model extends Laravel's `Authenticatable`, hashes assigned passwords, and hides password hashes and remember tokens from serialization.

A user cannot delete their own account through the management list. The Profile page provides Breeze's password-confirmed account deletion flow. Deleting a user also removes their avatar.

## Breeze authentication

The authentication controllers, `Auth/LoginRequest`, and `routes/auth.php` were generated from the Breeze Blade scaffold. Login throttling, password hashing, session regeneration, logout, and the password broker retain Breeze's behavior. Registration and profile field names were adapted to the POS schema, and the presentation uses the existing CSS.

The database cache store keeps rate-limit counters between requests. Default Breeze password rules are used. Email verification scaffolding is present but is not required to manage accounts; this assessment requires authentication.

Password reset mail uses the local `log` mailer by default. Reset links are recorded in `storage/logs/laravel.log`; real email delivery requires configuring a mail service.

## Existing TFA7 features

Customer and user forms still display validation errors and old input. User edits retain the validated avatar upload workflow:

- Real raster images up to 2 MB, with a maximum of 4096 pixels on either side
- Intervention Image preparation as a 256 x 256 JPEG
- Storage on the public disk with only a relative path saved in the database
- A placeholder for accounts without a picture
- Cleanup of replaced files and failed database updates

Avatars are now served through an authenticated route. Keep `public/storage` unlinked so the web server cannot serve those images before Laravel checks authentication. If you created the optional TFA7 storage link, remove it with:

```sh
php artisan storage:unlink
```

The finished setup does not create that link.

## Main files

```text
routes/
  web.php
  auth.php
app/Http/Controllers/
  CustomerController.php
  UserController.php
  PageController.php
  ProfileController.php
  Auth/
app/Http/Requests/
  CustomerRequest.php
  UserRequest.php
  ProfileUpdateRequest.php
  Auth/LoginRequest.php
app/Models/
  Customer.php
  User.php
app/Services/AvatarStorage.php
database/migrations/
  0001_01_01_000000_create_users_table.php
  2026_11_21_000001_create_customers_table.php
  2026_11_21_000002_add_avatar_to_users_table.php
  2026_11_22_000001_add_authentication_fields_to_users_table.php
  2026_11_22_000002_create_cache_tables.php
resources/views/
  layouts/app.blade.php
  auth/
  customers/
  users/
  profile/edit.blade.php
  dashboard.blade.php
public/css/style.css
tests/Feature/
```

## Inspect and test

```sh
php artisan route:list --except-vendor
php artisan migrate:status
php artisan test
```

The suite covers Breeze authentication and password flows, guest redirects for all resource actions, authenticated account management, registration fields, password hashing, session renewal, login throttling, profile deletion, and the earlier forms and avatar features.

The original directory tests use authenticated test fixtures so their record counts remain independent of the login identity. Access-control tests and the Breeze tests also use persisted accounts. Tests use an in-memory SQLite database and a fake disk for uploads, with real image preparation.

## Suggested screenshots

The worksheet does not explicitly require screenshots. If evidence is requested, capture:

1. A protected account URL redirecting to the login page while logged out.
2. The registration page.
3. Login followed by access to Customer Accounts or User Accounts.
4. Account management controls while signed in.
5. Logout followed by another redirect to login when reopening a protected page.

The resource route registrations and controller method names can be shown in code screenshots if requested.

## GitHub submission

Upload the **contents of `corner-store-pos-tfa8`** to a new repository. Keep `artisan`, `composer.json`, and `README.md` at the repository root, and include dotfiles such as `.env.example`, `.gitignore`, and `.github/workflows/tests.yml`.

Do not upload only the ZIP file. The generated `.env`, SQLite database, avatar uploads, logs, caches, and `vendor` folder are excluded. Migrations and seeders recreate the sample database, so a SQL export is unnecessary.

The included GitHub Actions workflow installs PHP dependencies and runs the tests. This deliverable is for local use; no public deployment is included.

## References

- [Laravel resource controllers](https://laravel.com/docs/13.x/controllers#resource-controllers)
- [Laravel Breeze source](https://github.com/laravel/breeze)
- [Laravel authentication](https://laravel.com/docs/13.x/authentication)
- [Laravel routing middleware groups](https://laravel.com/docs/13.x/routing#route-group-middleware)
