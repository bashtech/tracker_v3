@extends('application.base')

@section('content')

    @component ('application.components.division-heading', [$division])
        @slot ('icon')
            <img src="{{ getDivisionIconPath($division->abbreviation) }}" class="division-icon-large" />
        @endslot
        @slot ('heading')
            <span class="hidden-xs">{{ $division->name }}</span>
            <span class="visible-xs">{{ $division->abbreviation }}</span>
        @endslot
        @slot ('subheading')
            {{ $division->description }}
        @endslot
    @endcomponent

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <p>Enter your twig template in the text area below. </p>
                <form action="{{ route('division.update-structure', $division->abbreviation) }}" method="post">
                <textarea name="structure" id="structure" class="form-control" rows="10"
                          style="font-family: Menlo, Monaco, Consolas, monospace; resize: vertical;"
                >{{ $division->structure }}</textarea>
                </form>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <pre><code name="generated-structure">{# Generated #}</code></pre>
            </div>
        </div>
    </div>


@stop

<script>
    $ ('name[generate-code]').click (function (e) {
        e.preventDefault ();

//    $.ajax('')
    })
</script>