<?php

namespace Tests\Feature;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => Hash::make('oldpassword'),
            'role' => 'ustadz',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Satu',
            'username' => 'admin-satu',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    public function test_password_reset_request_works(): void
    {
        $response = $this->post(route('password.email'), [
            'username' => 'testuser',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('password_resets', [
            'username' => 'testuser',
        ]);
    }

    public function test_password_reset_request_does_not_reveal_username_existence(): void
    {
        $response = $this->post(route('password.email'), [
            'username' => 'nonexistent',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('password_resets', [
            'username' => 'nonexistent',
        ]);
    }

    public function test_password_reset_link_works(): void
    {
        $token = Str::random(64);

        PasswordReset::create([
            'username' => 'testuser',
            'token' => Hash::make($token),
            'requested_at' => now(),
            'token_created_at' => now(),
        ]);

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_password_reset_with_invalid_token_fails(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'invalid-token']));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('error');
    }

    public function test_password_reset_with_expired_token_fails(): void
    {
        $token = Str::random(64);

        PasswordReset::create([
            'username' => 'testuser',
            'token' => Hash::make($token),
            'requested_at' => now()->subHours(2),
            'token_created_at' => now()->subHours(2),
        ]);

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('error', 'Token tidak valid atau sudah kadaluarsa.');
    }

    public function test_password_reset_successfully_updates_password(): void
    {
        $token = Str::random(64);
        $selector = substr($token, 0, 8);

$reset = PasswordReset::create([
            'username' => 'testuser',
            'selector' => $selector,
            'token' => Hash::make($token),
            'requested_at' => now(),
            'token_created_at' => now(),
        ]);

        // Debug: check if reset exists
        $created = $reset->fresh();
        $this->assertNotNull($created, 'Reset should exist in database');
        $this->assertNotNull($created->selector, 'Selector should be saved');
        $this->assertEquals($selector, $created->selector);

        $response = $this->post(route('password.update'), [
            'username' => 'testuser',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $this->assertTrue(Hash::check('newpassword123', $this->user->fresh()->password));
        $this->assertDatabaseMissing('password_resets', ['username' => 'testuser']);
    }

    public function test_password_reset_token_is_single_use(): void
    {
        $token = Str::random(64);
        $selector = substr($token, 0, 8);

        PasswordReset::create([
            'username' => 'testuser',
            'selector' => $selector,
            'token' => Hash::make($token),
            'requested_at' => now(),
            'token_created_at' => now(),
        ]);

        $this->post(route('password.update'), [
            'username' => 'testuser',
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response = $this->post(route('password.update'), [
            'username' => 'testuser',
            'token' => $token,
            'password' => 'anotherpassword',
            'password_confirmation' => 'anotherpassword',
        ]);

        $response->assertSessionHas('error', 'Token tidak valid.');
    }

    public function test_admin_can_issue_reset_link(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.password-requests.issue', $this->user));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('reset_link');

        $this->assertDatabaseHas('password_resets', [
            'username' => 'testuser',
        ]);
    }

    public function test_admin_can_destroy_password_reset_request(): void
    {
        PasswordReset::create([
            'username' => 'testuser',
            'token' => Hash::make(Str::random(64)),
            'requested_at' => now(),
            'token_created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.password-requests.destroy', $this->user));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('password_resets', ['username' => 'testuser']);
    }

    public function test_non_admin_cannot_access_admin_password_requests(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.password-requests.index'))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->post(route('admin.password-requests.issue', $this->user))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('admin.password-requests.destroy', $this->user))
            ->assertForbidden();
    }
}