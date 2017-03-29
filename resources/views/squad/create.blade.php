@extends('application.base')
@section('content')

    @component ('application.components.division-heading')
        @slot ('icon')
            <img src="{{ getDivisionIconPath($division->abbreviation) }}" />
        @endslot
        @slot ('heading')
            {{ $division->name }} Division
        @endslot
        @slot ('subheading')
            {{ $division->description }}
        @endslot
    @endcomponent

    <div class="container-fluid">
        <form id="create-squad" method="post" class="margin-top-20"
              action="{{ route('storeSquad', [$division->abbreviation, $platoon->id]) }}">
            @include('squad.forms.edit-squad-form', ['actionText' => 'Create New'])
        </form>
    </div>

@stop

