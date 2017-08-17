<div class="row">
    <div class="col-md-9">
        <div class="panel panel-filled">
            <div class="panel-body">
                @foreach ($members->groupBy('rank.name') as $rank=>$members)
                    <div class="panel m-b-none">
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>{{ $rank }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td class="text-right text-muted slight">{{ $member->last_promoted }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-accent">Total</td>
                                    <td class="text-accent text-right">{{ $members->count() }}</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-filled">
            <div class="panel-body">
                <canvas class="promotions-chart"
                        data-labels="{{ json_encode($ranks->values()) }}"
                        data-values="{{ json_encode($counts) }}"
                ></canvas>
            </div>
        </div>
    </div>
</div>