@include('application.partials.errors')
<div class="panel panel-filled">
    <div class="panel-heading">{{ $action }}</div>
    <div class="panel-body">

        <div class="form-group {{ $errors->has('tag_list') ? ' has-error' : null }}">
            {!! Form::label('tag_list', 'Tags', ['class' => 'slight text-muted']) !!}
            {!! Form::select('tag_list[]', App\Tag::pluck('name', 'id'), null, ['id' => 'select-tags', 'class' => 'form-control', 'multiple', 'style' => 'width: 100%;']) !!}
        </div>

        <div class="form-group {{ $errors->has('body') ? ' has-error' : null }}">
            {!! Form::label('body', 'Content', ['class' => 'slight text-muted']) !!}
            {!! Form::textarea('body', null, ['class' => 'form-control resize-vertical', 'required', 'rows' => 2]) !!}
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-3 form-group">
                {!! Form::label('type', 'Note Type', ['class' => 'slight text-muted']) !!}
                {!! Form::select('type', App\Note::allNoteTypes(), null, ['class' => 'form-control']) !!}
            </div>

            <div class="col-sm-3 form-group">
                {!! Form::label('forum_thread_id', 'Forum Thread ID', ['class' => 'slight text-muted']) !!}
                {!! Form::number('forum_thread_id', null, ['class' => 'form-control']) !!}
            </div>

            <div class="col-xs-3 form-group">
                <a href="{{ url()->previous() }}" class="btn btn-default btn-block" style="margin-top:23px">Cancel</a>
            </div>

            <div class="col-xs-3 form-group">
                <button type="submit" class="btn btn-default btn-block" style="margin-top:23px">Submit</button>
            </div>
        </div>
    </div>
</div>

@section('footer_scripts')
    <script type="text/javascript">
        $("#select-tags").select2({
            maximumSelectionLength: 2,
            placeholder: 'Select tag(s)'
        });
    </script>
@endsection