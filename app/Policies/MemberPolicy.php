<?php

namespace App\Policies;

use App\Member;
use App\Rank;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        // MSgts, SGTs, developers have access to all members
        if ($user->isRole(['admin', 'sr_ldr']) || $user->isDeveloper()) {
            return true;
        }
    }

    /**
     * Can the user update the given member?
     *
     * @param User $user
     * @param Member $member
     * @return bool
     */
    public function update(User $user, Member $member)
    {
        $userDivision = $user->member->primary->first();
        $memberDivision = $member->primary->first();

        // user can update self
        if ($user->member->id == $member->id) {
            return true;
        }

        // Officers can update anyone within own platoon (or squad)
        if ($user->isRole('officer') && $user->member->platoon == $member->platoon) {
            return true;
        }

        // Jr leaders (CPl) can update anyone within division
        if ($user->isRole('jr_ldr') && $userDivision->id == $memberDivision->id) {
            return true;
        }

        return false;
    }

    /**
     * Determines policy for removing members
     *
     * @param User $user
     * @param Member $member
     * @return bool
     */
    public function delete(User $user, Member $member)
    {
        // can't delete yourself
        if ($member->id == $user->member->id) {
            return false;
        }

        // use the abbreviation in case id changes for some reason
        if ($user->member->rank_id < Rank::whereAbbreviation('sgt')->first()->id) {
            return false;
        }

        return true;
    }
}
