@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body ss-card">
                    <h2>Status</h2>
                    <p>

                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <h1 class="lead m-4">Factuur {{ $factuur->nummer }}</h1>
                <div class="card-body ss-card-left">
                    <p>Project: <a
                            href="{{ route('project', $factuur->projectnr.'-'.Str::slug($factuur->getProject->projectnaam)) }}">{{ $factuur->getProject->projectnaam }}</a>
                    </p>
                    @if($factuur->getProject->getClient)
                    <p>Klant: {{ $factuur->getProject->getClient->bedrijfsnaam }}</p>
                    @endif
                    <p>Bedrag: €{{ $factuur->bedrag }}</p>

                    @if($factuur->getFactor)
                    <p> <strong>Factor factuur</strong></p>
                    @endif

                    <p>Factuurdatum: {{ $factuur->datum->format('d-m-Y') }}</p>

                    <p>Vervaldatum: {{ $factuur->datum->addDays($factuur->krediet)->format('d-m-Y') }}</p>
                    <p>Betaaltermijn: {{ $factuur->krediet }} dagen</p>
                    @if($factuur->voldaan === 'J' )
                    <p class="mt-4">Voldaan op {{ $factuur->voldaanop->format('d-m-Y') }}</p>
                    @else
                    <p class="mt-4"><strong>Factuur is nog niet betaald</strong></p>
                    @endif

                </div>
            </div>
        </div>
        @if(!empty($factuur->map) || !empty($factuur->getBon->map))
        <div class="col-md-4">
            <div class="card">
                <h1 class="lead m-4">Documenten</h1>
                <div class="card-body ss-card-left">
                    @if(!empty($factuur->map))
                    <a class="btn btn-primary" href="https://www.fqr.nl{{ $factuur->map }}" target="_blank">Bekijk
                        factuur</a>
                    @endif
                    @if(!empty($factuur->getBon->map) && $factuur->getBon->map !== 'Geen bon nodig' &&
                    $factuur->getBon->map !== 'Samengevoegd met de factuur')
                    <a class="btn btn-primary" href="https://www.fqr.nl{{ $factuur->getBon->map }}"
                        target="_blank">Bekijk
                        Bon</a>
                    @endif

                </div>
            </div>
        </div>
        @endif
        @if($projectFacturen->count() > 0)
        <div class="col-md-4">
            <div class="card">
                <h1 class="lead m-4">Nog {{ $projectFacturen->count() }} facturen van
                    {{ $factuur->getProject->projectnaam }}
                </h1>
                <div class="card-body ss-card-left">
                    @foreach($projectFacturen as $projectFactuur)
                    <p><a href="{{ route('showFactuur', $projectFactuur->id) }}">{{ $projectFactuur->nummer }} -
                            €{{ $projectFactuur->bedrag }}</a></p>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <div class="col-12">
            <a href="{{ route('facturen') }}" class="btn btn-primary mt-4">Terug naar het facturenoverzicht</a>
        </div>
    </div>
</div>
@endsection
