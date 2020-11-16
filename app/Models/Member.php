<?php

namespace App\Models;

use App\Presenters\MemberPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Member
 *
 * @package App
 * @method static whereName($name)
 */
class Member extends \Illuminate\Database\Eloquent\Model
{
    use \App\Models\Member\HasCustomAttributes, \App\Activities\RecordsActivity, \Illuminate\Database\Eloquent\SoftDeletes;

    public const UNVERIFIED_EMAIL_GROUP_ID = 3;
    /**
     * @var array
     */
    protected static $recordEvents = [];
    protected $casts = ['pending_member' => 'boolean', 'flagged_for_inactivity' => 'boolean'];
    /**
     * @var array
     */
    protected $fillable = [
        'name', 'clan_id', 'platoon_id', 'squad_id', 'position_id', 'division_id', 'posts', 'join_date',
        'last_activity', 'last_ts_activity', 'last_promoted_at', 'recruiter_id'
    ];
    /**
     * @var array
     */
    protected $dates = [
        'join_date',
        'last_activity',
        'last_promoted_at',
        // sgt info
        'last_trained_at',
        'xo_at',
        'co_at',
    ];

    /**
     * @return MemberPresenter
     */
    public function present()
    {
        return new \App\Presenters\MemberPresenter($this);
    }

    /**
     * relationship - user has one member
     */
    public function user()
    {
        return $this->hasOne(\App\Models\User::class);
    }

    /**
     * @return HasMany
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\Note::class, 'member_id')->orderBy('created_at', 'desc');
    }

    /**
     * @param $position
     * @return Model
     */
    public function assignPosition($position)
    {
        $newPosition = $position instanceof \App\Models\Position ? $position : \App\Models\Position::whereName(strtolower($position))->firstOrFail();
        // reset assignments for specific positions
        if (in_array($newPosition->name,
            ["Commanding Officer", "Executive Officer", "General Sergeant", "Clan Admin"])) {
            $this->platoon_id = 0;
            $this->squad_id = 0;
        }
        if ($newPosition->name == 'Executive Officer') {
            $this->xo_at = now();
            $this->co_at = null;
        }
        if ($newPosition->name == 'Commanding Officer') {
            $this->co_at = now();
            $this->xo_at = null;
        }
        return $this->position()->associate($newPosition);
    }

    /**
     * relationship - member belongs to a position
     */
    public function position()
    {
        return $this->belongsTo(\App\Models\Position::class);
    }

    /**
     * @param $rank
     * @return Model
     */
    public function assignRank($rank)
    {
        return $this->rank()->associate(\App\Models\Rank::whereName(strtolower($rank))->firstOrFail());
    }

    /**
     * relationship - member belongs to a rank
     */
    public function rank()
    {
        return $this->belongsTo(\App\Models\Rank::class);
    }

    /**
     * Enforce a singleton relationship for squad leaders
     *
     * Prevents members from being a squad leader of more than one squad
     *
     * @return HasOne
     */
    public function squadLeaderOf()
    {
        return $this->hasOne(\App\Models\Squad::class, 'leader_id');
    }

    /**
     * Handle Staff Sergeant assignments
     * division/
     *
     * @return BelongsToMany
     */
    public function ssgtAssignment()
    {
        return $this->belongsToMany(\App\Models\Division::class, 'staff_sergeants')->withTimestamps();
    }

    /**
     * Resets member's positions and division assignments
     * including part-time divisions
     */
    public function resetPositionAndAssignments()
    {
        $this->update([
            'division_id' => 0, 'platoon_id' => 0, 'squad_id' => 0, 'position_id' => 1,
            'flagged_for_inactivity' => false
        ]);
    }

    /**
     * Handle Staff Sergeant assignments
     * division/
     *
     * @return BelongsToMany
     */
    public function partTimeDivisions()
    {
        return $this->belongsToMany(\App\Models\Division::class, 'division_parttimer')->withTimestamps();
    }

    /**
     * relationship - member belongs to a platoon
     */
    public function platoon()
    {
        return $this->belongsTo(\App\Models\Platoon::class);
    }

    /**
     * relationship - member belongs to a squad
     */
    public function squad()
    {
        return $this->belongsTo(\App\Models\Squad::class);
    }

    /**
     * relationship - member has many divisions
     */
    public function division()
    {
        return $this->belongsTo(\App\Models\Division::class);
    }

    /**
     * @return BelongsTo
     */
    public function recruiter()
    {
        return $this->belongsTo(\App\Models\Member::class, 'recruiter_id', 'clan_id');
    }

    public function recruits()
    {
        return $this->hasMany(\App\Models\Member::class, 'recruiter_id', 'clan_id');
    }

    public function trainer()
    {
        return $this->belongsTo(\App\Models\Member::class, 'last_trained_by', 'clan_id');
    }

    /**
     * @return HasOne
     */
    public function leave()
    {
        return $this->hasOne(\App\Models\Leave::class, 'member_id', 'clan_id');
    }

    /**
     * @return mixed
     */
    public function expiredLeave()
    {
        return $this->hasOne(\App\Models\Leave::class)->where('end_date', '<', \Carbon\Carbon::today());
    }

    /**
     * @return mixed
     */
    public function isPending()
    {
        if ($this->memberRequest) {
            return $this->memberRequest()->pending()->exists();
        }
        return false;
    }

    /**
     * @return HasOne
     */
    public function memberRequest()
    {
        return $this->hasOne(\App\Models\MemberRequest::class, 'member_id', 'clan_id');
    }

    /**
     * @return mixed
     */
    public function activeLeave()
    {
        return $this->hasOne(\App\Models\Leave::class)->where('end_date', '>', \Carbon\Carbon::today());
    }
    /**
     * -------------------------------------
     * Policy object refers to these methods
     * -------------------------------------
     */
    /**
     * @return BelongsToMany
     */
    public function handles()
    {
        return $this->belongsToMany(\App\Models\Handle::class)->withPivot('value');
    }

    /**
     * @param  Squad  $squad
     * @return bool
     */
    public function isSquadLeader(\App\Models\Squad $squad)
    {
        return $this->clan_id === $squad->leader_id;
    }

    /**
     * @param  Platoon  $platoon
     * @return bool
     */
    public function isPlatoonLeader(\App\Models\Platoon $platoon)
    {
        return $this->clan_id === $platoon->leader_id;
    }

    /**
     * Check to see if the member is a division leader
     * and also assigned to the given division
     *
     * @param  Division  $division
     * @return bool
     */
    public function isDivisionLeader(\App\Models\Division $division)
    {
        if ($this->division->id === $division->id && in_array($this->position_id, [5, 6])) {
            return true;
        }
        return false;
    }

    /**
     * @param $rank
     * @return bool
     */
    public function isRank($rank)
    {
        if (!$this->rank instanceof \App\Models\Rank) {
            return false;
        }
        if (is_array($rank)) {
            return in_array(strtolower($this->rank->abbreviation), array_map('strtolower', $rank));
        }
        return $this->rank->abbreviation === $rank;
    }

    public function getUrlParams()
    {
        return [$this->clan_id, $this->rank->abbreviation . '-' . $this->name];
    }

    /**
     * @return HasMany
     */
    public function memberRequests()
    {
        return $this->hasMany(\App\Models\MemberRequest::class, 'requester_id', 'clan_id');
    }
}