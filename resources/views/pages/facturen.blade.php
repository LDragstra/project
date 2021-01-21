@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            @foreach($facturen as $factuur)
            <div class="ss-card m-4 p-2 row">
                <div class="col-md-6">
                    <a class="mb-4" href="{{ route('showFactuur', $factuur->id) }}">{{ $factuur->nummer }}</a>
                    @if($factuur->voldaan ==='J' )*@endif
                    <br>
                    <br>
                    @if(!$factuur->map && !$factuur->base64 && $factuur->bon_id)
                    <button class="btn btn-danger" onclick="deleteSalesOrder({{$factuur->id}})"
                        id="delete-{{$factuur->id}}"
                        title="Verwijder verkooporder en zet bon terug naar nog te factureren" aria-label="Delete">
                        <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    @endif
                    @if($factuur->map)

                    <button type="button" id="openModal"
                        onclick="setTexts({{$factuur->nummer}},{{$factuur->id}},'{{$factuur->getEmailAddress()}}')"
                        class="btn btn-sm btn-primary" data-toggle="modal" data-target="#invoiceModal">
                        <i class="fa fa-envelope" aria-hidden="true"></i> Factuur mailen
                    </button>

                    <button id="telling-{{$factuur->id}}"
                        class="btn btn-sm btn-secondary">{{$factuur->getLogs->where('opmerking', 'Factuur verstuurd naar klant')->count()}}</button> @if($factuur->getLogs->where('opmerking', 'Factuur verstuurd naar klant')->count() >= 1) {{ $factuur->getLogs->where('opmerking', 'Factuur verstuurd naar klant')->last()->datumtijd->diffForHumans() . ' gemaild' }} @endif
                    @endif
                    <input type="hidden" name="form-id" value="{{$factuur->id}}">
                </div>
                <div class="col-md-6">
                    <p><small>
                            Bedrag: €{{ number_format($factuur->bedrag,2,',','.') }} <br>
                            @if($factuur->getProject->getClient)
                            Klant: {{ $factuur->getProject->getClient->bedrijfsnaam }}
                            @endif
                        </small><br>
                        <small>Project: <a
                                href="{{ route('project', $factuur->projectnr.'-'.Str::slug($factuur->getProject->projectnaam)) }}">{{ $factuur->getProject->projectnaam }}</a></small>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="col-3"><small>* factuur is betaald</small></div>
    <div class="mt-4 col-12">
        {{ $facturen->links() }}
    </div>
</div>
<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="lead">E-mailadres</h3>
                <input type="email" name="invoiceEmail" id="invoiceEmail" class="form-control" value="" required>
                <h3 class="lead mt-4">Tekst zoals die in de mail komt</h3>
                <input type="hidden" name="hiddenModal" id="hiddenModal" value="">
                <textarea name="text" id="text" rows="7" style="width:100%;">Geachte heer/mevrouw,
                    
In de bijlage kunt u de factuur van onze verleende diensten vinden.

Wij verzoeken u vriendelijk het verschuldigde bedrag binnen de gestelde factuurtermijn te voldoen.</textarea>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
                <button type="button" onclick="sendForm()" data-dismiss="modal"
                    class="btn btn-primary">Verzenden</button>
            </div>
        </div>
    </div>
</div>

@endsection