<?php

namespace App\Policies;

use App\Models\Division;
use App\Models\Member;
use App\Models\Rank;
use App\Models\User;
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
     * @return bool
     */
    public function create(User $user)
    {
        // member role cannot recruit members
        if ($user->role_id > 1) {
            return true;
        }

        return false;
    }

    /**
     * Can the user update the given member?
     *
     * @return bool
     */
    public function update(User $user, Member $member)
    {
        // can't edit yourself
        if ($member->id === auth()->user()->member_id) {
            return false;
        }

        $userDivision   = $user->member->division;
        $memberDivision = $member->division;

        // officers can update anyone within division
        if ($memberDivision instanceof Division) {
            if (in_array($user->role_id, [3, 4]) && $userDivision->id === $memberDivision->id) {
                return true;
            }
        }

        return false;
    }

    public function updateLeave()
    {
        return false;
    }

    public function reset(User $user)
    {
        return false;
    }

    public function view()
    {
        return true;
    }

    public function viewAny()
    {
        return true;
    }

    /**
     * Determines policy for removing members.
     *
     * @return bool
     */
    public function delete(User $user, Member $member)
    {
        // can't delete yourself
        if ($member->id === $user->member->id) {
            return false;
        }

        // use the abbreviation in case id changes for some reason
        if ($user->member->rank_id < Rank::whereAbbreviation('sgt')->first()->id) {
            return false;
        }

        return true;
    }

    public function managePartTime(User $user, Member $member)
    {
        // can edit yourself
        if ($member->id === auth()->user()->member_id) {
            return true;
        }

        if ($user->isRole('officer')) {
            return true;
        }

        return false;
    }

    public function promote(User $userPromoting, Member $memberBeingPromoted)
    {
        // only admin, sr_ldr, officer can promote
        if (!$userPromoting->isRole('officer')) {
            return false;
        }

        // can only promote up to one below your rank
        $rankAllowed = $userPromoting->member->rank_id - 1;
        if ($rankAllowed < $memberBeingPromoted->rank_id) {
            return false;
        }

        // can only promote within division
        if ($userPromoting->member->division_id !== $memberBeingPromoted->division_id) {
            return false;
        }

        return true;
    }

    public function manageIngameHandles(User $user, Member $member)
    {
        // can edit yourself
        if ($member->id === auth()->user()->member_id) {
            return true;
        }

        if ($user->isRole('officer')) {
            return true;
        }

        return false;
    }
}
