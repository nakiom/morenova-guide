<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Center;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CenterCredentialTest extends TestCase
{
    use RefreshDatabase;

    public function test_primary_user_can_change_a_sub_user_password(): void
    {
        [$manager, $subUser] = $this->centerUsers();

        $response = $this->actingAs($manager)->put(
            route('center.credentials.update', $subUser),
            [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ],
        );

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('center.credentials.index'));
        $this->assertTrue(Hash::check('new-password-123', $subUser->fresh()->password));
    }

    public function test_primary_user_needs_current_password_to_change_own_password(): void
    {
        [$manager] = $this->centerUsers();

        $response = $this->actingAs($manager)->from(route('center.credentials.edit', $manager))->put(
            route('center.credentials.update', $manager),
            [
                'current_password' => 'incorrect',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ],
        );

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('password', $manager->fresh()->password));
    }

    public function test_primary_user_cannot_change_users_from_another_center(): void
    {
        [$manager] = $this->centerUsers();
        $otherCenter = Center::create(['name' => 'Other Center']);
        $otherUser = User::factory()->create(['center_id' => $otherCenter->id]);

        $this->actingAs($manager)
            ->put(route('center.credentials.update', $otherUser), [
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertForbidden();
    }

    public function test_sub_user_cannot_manage_center_credentials(): void
    {
        [, $subUser] = $this->centerUsers();

        $this->actingAs($subUser)
            ->get(route('center.credentials.index'))
            ->assertForbidden();
    }

    /**
     * @return array{User, User}
     */
    private function centerUsers(): array
    {
        $center = Center::create(['name' => 'Test Center']);
        $manager = User::factory()->create([
            'center_id' => $center->id,
            'parent_user_id' => null,
            'is_admin' => false,
        ]);
        $subUser = User::factory()->create([
            'center_id' => $center->id,
            'parent_user_id' => $manager->id,
            'is_admin' => false,
        ]);

        return [$manager, $subUser];
    }
}
