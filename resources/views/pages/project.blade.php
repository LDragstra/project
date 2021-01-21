@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body ss-card">
            <h1>{{ $project->projectnaam }} - @if($project->getClient)
                <small>{{ $project->getClient->bedrijfsnaam }}</small></h1>
            @else

            </h1>
            <p>Klant is niet juist gekoppeld. Waarschijnlijk gekoppeld d.m.v. het oude klantid. Graag opnieuw
                koppelen
                via VAP.</p>
            @endif
            <h2>Soort: {{$brutowinst['soortProject'] }}</h2>
            <hr>
            <div class="row mt-4">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body ss-card">
                            <h2>Stats</h2>
                            €{{number_format($brutowinst['gefactureerd'],2,',','.')}} gefactureerd <br>
                            @if($brutowinst['soortProject'] == 'Aanneemsom')
                            €{{number_format($brutowinst['omzet'],2,',','.')}} afgesproken aanneemsom<br>
                            @elseif ($brutowinst['soortProject'] == 'Dagtarief')
                            €{{number_format($brutowinst['omzet'],2,',','.')}} omzet d.m.v. dagtarief (
                            {{$brutowinst['tijd'] / 8}} dag en)<br>
                            @elseif ($brutowinst['soortProject'] == 'Uurtarief')
                            €{{number_format($brutowinst['omzet'],2,',','.')}} omzet d.m.v. uurtarief (
                            {{$brutowinst['tijd']}} uren)<br>
                            @endif

                            @if($brutowinst['afrondingFactureren'] < 0) Teveel gefactureerd:
                                {{number_format(str_replace('-', '', $brutowinst['afrondingFactureren']),2,',','.')}}
                                @else Nog te factureren:
                                €{{number_format($brutowinst['afrondingFactureren'],2,',','.')}} @endif <br> Berekening
                                van omzet: €{{str_replace('.',',',$brutowinst['omzetBerekening'])}}

                                @foreach($brutowinst['details'] as $detail)
                                {{$detail}} <br>
                                @endforeach
                        </div>
                    </div>
                </div>
                @if($projectFacturen->count() > 0)
                <div class="col-md-4">
                    <div class="card">
                        <h1 class="lead m-4">Er zijn {{ $projectFacturen->count() }} facturen voor dit project</h1>
                        <div class="card-body">
                            @foreach($projectFacturen as $projectFactuur)
                            <p><a href="{{ route('showFactuur', $projectFactuur->id) }}">{{ $projectFactuur->nummer }}
                                    -
                                    €{{ $projectFactuur->bedrag }}</a></p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @if(array_key_exists('meerwerk', $brutowinst)) {
                <div class="col-md-3">
                    <h2>Meerwerk</h2>
                    <ul>
                        @foreach($brutowinst['meerwerk'] as $meerwerk)
                        <li>€{{number_format($meerwerk->bedrag,2,',','.')}} - {{$meerwerk->uren}} uren -
                            {{$meerwerk->omschr}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="row mt-4">

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-bod">
                            <div class="progress">
                                <div class="progress-bar bg-danger" data-toggle="tooltip" data-placement="top"
                                    title="Inkoop €{{ $inkoopBedrag }} ({{round($percentageInkoop,2)}}%)"
                                    role="progressbar" style="width:{{$percentageInkoop}}%">
                                    Inkoop €{{$inkoopBedrag}}
                                </div>
                                <div class="progress-bar bg-success" data-toggle="tooltip" data-placement="top"
                                    title=" Gefactureerd €{{ $brutowinst['gefactureerd'] }} ({{round(100 - $percentageInkoop,2)}}%)"
                                    role="progressbar" style="width:{{ 100-$percentageInkoop }}%">
                                    Gefactureerd €{{ $brutowinst['gefactureerd'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <a href="{{ url()->previous() }}" class="btn btn-primary mt-4">Vorige pagina</a>
        </div>
    </div>
</div>
@endsection