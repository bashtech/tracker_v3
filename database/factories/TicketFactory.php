<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $states = ['new', 'assigned', 'resolved'];
        $randomState = array_rand($states);

        return [
            'state' => $states[$randomState],
            'ticket_type_id' => \App\Models\TicketType::inRandomOrder()->first()->id,
            'caller_id' => \App\Models\User::inRandomOrder()->first()->id,
            'division_id' => \App\Models\Division::inRandomOrder()->active()->get()->first()->id,
            'description' => $this->faker->paragraph,
            'message_id' => Uuid::uuid4()->toString(),
            'owner_id' => $states[$randomState] === 'assigned'
                ? \App\Models\User::whereRoleId(5)->inRandomOrder()->first()->id
                : null,
        ];
    }
}
