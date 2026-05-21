<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_non_admin_user_is_forbidden(): void
    {
        $this->actingAs($this->customer())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_open_every_panel_page(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/reservas')->assertOk();
        $this->actingAs($admin)->get('/admin/carta')->assertOk();
        $this->actingAs($admin)->get('/admin/mesas')->assertOk();
    }

    public function test_admin_can_confirm_a_reservation(): void
    {
        $reservation = $this->makeReservation();

        $this->actingAs($this->admin())
            ->patch("/admin/reservas/{$reservation->id}", ['status' => 'confirmada'])
            ->assertRedirect();

        $this->assertSame('confirmada', $reservation->fresh()->status);
    }

    public function test_admin_can_create_a_menu_item(): void
    {
        $category = $this->makeCategory();

        $this->actingAs($this->admin())
            ->post('/admin/carta', [
                'category_id'      => $category->id,
                'name'             => 'Mai Tai',
                'description'      => 'Ron, lima y curaçao',
                'price'            => 9.5,
                'contains_alcohol' => true,
                'is_available'     => true,
                'emoji'            => null,
            ])
            ->assertRedirect('/admin/carta');

        $this->assertDatabaseHas('menu_items', [
            'name'  => 'Mai Tai',
            'price' => 9.5,
        ]);
    }

    public function test_non_admin_cannot_update_a_reservation(): void
    {
        $reservation = $this->makeReservation();

        $this->actingAs($this->customer())
            ->patch("/admin/reservas/{$reservation->id}", ['status' => 'confirmada'])
            ->assertForbidden();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function customer(): User
    {
        return User::factory()->create(['is_admin' => false]);
    }

    private function makeCategory(): Category
    {
        return Category::create([
            'name'       => 'Cócteles',
            'slug'       => 'cocteles-'.uniqid(),
            'type'       => 'drink',
            'sort_order' => 1,
        ]);
    }

    private function makeReservation(): Reservation
    {
        return Reservation::create([
            'user_id'          => $this->customer()->id,
            'contact_name'     => 'Antonio Malagueño',
            'contact_phone'    => '600111222',
            'reservation_date' => now()->toDateString(),
            'reservation_time' => '21:00',
            'adults'           => 2,
            'children'         => 0,
            'zone_preference'  => 'cualquiera',
            'status'           => 'pendiente',
        ]);
    }
}
