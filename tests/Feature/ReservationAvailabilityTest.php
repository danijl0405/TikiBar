<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_a_free_table_is_assigned_and_the_reservation_is_confirmed(): void
    {
        $table = $this->table('I-1', 4, 'interior');

        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload(['zone_preference' => 'interior']))
            ->assertRedirect('/reservas');

        $reservation = Reservation::sole();
        $this->assertSame($table->id, $reservation->restaurant_table_id);
        $this->assertSame('confirmada', $reservation->status);
    }

    public function test_a_table_taken_for_a_turn_is_not_assigned_again_in_the_same_turn(): void
    {
        $table = $this->table('I-1', 4, 'interior');
        $date = now()->addDay()->toDateString();
        $this->occupy($table, $date, '21:30');

        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload([
                'reservation_date' => $date,
                'reservation_time' => '21:30',
                'zone_preference'  => 'interior',
            ]))
            ->assertSessionHasErrors('reservation_time');

        // Only the pre-existing reservation remains — nothing new was created.
        $this->assertSame(1, Reservation::count());
    }

    public function test_a_table_taken_for_a_turn_is_free_again_in_another_turn(): void
    {
        $table = $this->table('I-1', 4, 'interior');
        $date = now()->addDay()->toDateString();
        $this->occupy($table, $date, '21:30');

        // Same table, same day, earlier turn -> allowed.
        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload([
                'reservation_date' => $date,
                'reservation_time' => '14:00',
                'zone_preference'  => 'interior',
            ]))
            ->assertRedirect('/reservas');

        $new = Reservation::where('reservation_time', '14:00')->sole();
        $this->assertSame($table->id, $new->restaurant_table_id);
    }

    public function test_an_invalid_turn_is_rejected(): void
    {
        $this->table('I-1', 4, 'interior');

        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload(['reservation_time' => '21:00']))
            ->assertSessionHasErrors('reservation_time');

        $this->assertSame(0, Reservation::count());
    }

    public function test_two_reservations_the_same_turn_get_different_tables(): void
    {
        $this->table('I-1', 4, 'interior');
        $this->table('I-2', 4, 'interior');
        $date = now()->addDay()->toDateString();

        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload(['reservation_date' => $date, 'zone_preference' => 'interior']));
        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload(['reservation_date' => $date, 'zone_preference' => 'interior']));

        $tableIds = Reservation::pluck('restaurant_table_id');
        $this->assertCount(2, $tableIds);
        $this->assertCount(2, $tableIds->unique(), 'Las dos reservas del mismo turno deben caer en mesas distintas.');
    }

    public function test_a_cancelled_reservation_frees_the_turn_again(): void
    {
        $table = $this->table('I-1', 4, 'interior');
        $date = now()->addDay()->toDateString();
        $this->occupy($table, $date, '21:30')->update(['status' => 'cancelada']);

        $this->actingAs(User::factory()->create())
            ->post('/reservas', $this->payload([
                'reservation_date' => $date,
                'reservation_time' => '21:30',
                'zone_preference'  => 'interior',
            ]))
            ->assertRedirect('/reservas');

        $confirmed = Reservation::where('status', 'confirmada')->sole();
        $this->assertSame($table->id, $confirmed->restaurant_table_id);
    }

    private function table(string $code, int $capacity, string $zone): RestaurantTable
    {
        return RestaurantTable::create([
            'code'      => $code,
            'capacity'  => $capacity,
            'zone'      => $zone,
            'is_active' => true,
        ]);
    }

    private function occupy(RestaurantTable $table, string $date, string $time): Reservation
    {
        return Reservation::create([
            'user_id'             => User::factory()->create()->id,
            'restaurant_table_id' => $table->id,
            'contact_name'        => 'Reserva previa',
            'contact_phone'       => '600000000',
            'reservation_date'    => $date,
            'reservation_time'    => $time,
            'adults'              => 2,
            'children'            => 0,
            'zone_preference'     => 'interior',
            'status'              => 'confirmada',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'contact_name'     => 'Cliente Test',
            'contact_phone'    => '600111222',
            'reservation_date' => now()->addDay()->toDateString(),
            'reservation_time' => '21:30',
            'adults'           => 2,
            'children'         => 0,
            'zone_preference'  => 'cualquiera',
            'notes'            => null,
        ], $overrides);
    }
}
