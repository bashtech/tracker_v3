<h3 class="m-b-xs text-uppercase m-t-xxxl">
    {{ str_plural($division->locality('platoon')) }}

    @can('create', [App\Platoon::class, $division])
        <a href="{{ route('createPlatoon', $division->abbreviation) }}"
           class="btn btn-default"><i class="fa fa-plus text-success"></i> NEW
        </a>
    @endcan
</h3>

<hr>

<div class="row">
    @forelse ($platoons as $platoon)
        <div class="col-md-6">
            <a href="{{ route('platoon', [$division->abbreviation, $platoon->id]) }}"
               class="panel panel-filled">
                <div class="panel-body">
                    <h4 class="m-b-none">
                        {{ $platoon->name }}
                        <label class="badge pull-right">{{ $platoon->members_count }} Assigned</label>
                    </h4>

                    @if(is_object($platoon->leader))
                        <p class="list-group-item-text">{{ $platoon->leader->present()->rankName }}</p>
                    @else
                        <p class="list-group-item-text">Unfilled</p>
                    @endif
                </div>
            </a>
        </div>
    @empty
        <div class="col-md-12">
            <div class="panel panel-filled panel-c-danger text-muted">
                <div class="panel-body">
                    <h4 class="m-b-none text-uppercase">
                        No {{ str_plural($division->locality('platoon')) }} Found
                    </h4>
                </div>
            </div>
        </div>
    @endforelse
</div>


