<?php

namespace Database\Seeders;

use App\Models\Census;
use App\Models\Division;
use App\Models\Member;
use App\Models\Platoon;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach (Division::all() as $division) {
            $this->command->info("Adding division members - {$division->name}");

            $this->generateDivisionMembers($division);
            $this->generateCensusData($division);
        }

        // generate user
        $member = Member::inRandomOrder()->first();
        User::factory()->create([
            'name' => $member->name,
            'member_id' => $member,
            'role_id' => 5,
        ]);
    }

    protected function generateCensusData($division): void
    {
        for ($i = 1; $i < 7; $i++) {
            Census::factory()->create([
                'division_id' => $division,
                'created_at' => now()->subWeeks($i),
            ]);
        }
    }

    private function generateDivisionMembers($division)
    {
        $platoons = Platoon::factory()->count(rand(2, 5))->create([
            'division_id' => $division,
        ]);

        foreach ($platoons as $platoon) {
            $squads = Squad::factory()->count(rand(1, 3))->create([
                'platoon_id' => $platoon,
            ]);

            foreach ($squads as $squad) {
                Member::factory()->ofTypeMember()->count(rand(5, 20))->create([
                    'division_id' => $division,
                    'platoon_id' => $platoon,
                    'squad_id' => $squad,
                ]);
            }
        }
    }
}
