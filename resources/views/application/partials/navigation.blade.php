<ul class="nav luna-nav">

    <li class="{{ set_active('home') }}">
        <a href="{{ route('home') }}">Dashboard</a>
    </li>

    <li class="{{ set_active('statistics') }}">
        <a href="{{ route('statistics') }}">Statistics</a>
    </li>

    <li>

        <a href="#user-cp" data-toggle="collapse" aria-expanded="false">
            {{ auth()->user()->name }}
            @if (session('impersonating'))
                <i class="fa fa-user-secret text-info" title="Currently Impersonating"></i>
            @endif
            @if (auth()->user()->isDeveloper())
                <i class="fa fa-shield text-danger" title="Dev mode enabled"></i>
            @endif
            <span class="sub-nav-icon"> <i class="stroke-arrow"></i> </span>
        </a>

        <ul id="user-cp" class="nav nav-second collapse">
            <li class="no-select">
                <a href="#">
                    <small class="text-muted text-uppercase slight">
                        Role: <strong>{{ auth()->user()->role->name }}</strong>
                    </small>
                </a>
            </li>

            @if (session('impersonating'))
                <li>
                    <a href="{{ route('end-impersonation') }}">
                        <strong>End Impersonation</strong>
                        <i class="fa fa-user-secret"></i>
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ route('member', auth()->user()->member->clan_id) }}">
                    Member Profile
                </a>
            </li>

            <li>
                <a href="{{ doForumFunction([auth()->user()->member->clan_id], 'forumProfile') }}">Forum Profile</a>
            </li>

            @if(Auth::user()->isRole('admin'))
                <li class="{{ set_active('admin') }}">
                    <a href="{{ route('admin') }}">Admin CP</a>
                </li>
            @endif

            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout <i class="fa fa-lock text-muted"></i>
                </a>
            </li>

        </ul>
    </li>

    @if (auth()->user()->role_id > 1)
        <li>
            <a href="#tools" data-toggle="collapse" aria-expanded="false">
                Tools<span class="sub-nav-icon"> <i class="stroke-arrow"></i> </span>
            </a>
            <ul id="tools" class="nav nav-second collapse">
                @can('create', App\Member::class)
                    <li><a href="{{ route('recruiting.initial') }}">Add New Recruit</a></li>
                @endcan
            </ul>
        </li>
    @endif

    <li class="nav-category">
        Members
    </li>

    <li class="{{ set_active('members') }}">
        <a href="{{ route('members') }}">All Members</a>
    </li>

    <li class="{{ set_active('sergeants') }}">
        <a href="{{ route('sergeants') }}">Sergeants</a>
    </li>

    <li class="nav-category">
        Application
    </li>

    @can('manage-issues', App\User::class)
        <li class="{{ set_active('issues') }}">
            <a href="{{ route('github.issues') }}">Issue Reports</a>
        </li>
    @endcan

    <li class="{{ set_active('help') }}">
        <a href="{{ route('help') }}">Documentation</a>
    </li>
</ul>