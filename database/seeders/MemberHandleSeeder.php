<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberHandle;
use Illuminate\Database\Seeder;

class MemberHandleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Member::whereRelation('division', 'name', '!=', 'Floater')->get() as $member) {
            MemberHandle::factory()->create([
                'member_id' => $member,
                'handle_id' => $member->division->handle_id,
            ]);
        }
    }
}
