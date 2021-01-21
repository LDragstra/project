@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Initiele betalingen die klaar staan</h5>
                        @if($uitTeKeren)
                            <p class="card-text">Facturen waarvan het factoring bedrag nog uitbetaald moet worden.</p>
                            <a href="{{ route('initiele-betaling')}}" class="btn btn-primary">{{ $uitTeKeren }} eerste
                                betalingen</a>
                        @else
                            <p class="card-text">Alle betalingen zijn bij. Bij het aanmaken van nieuwe facturen zal deze
                                lijst worden aangevuld.</p>
                            <button class="btn btn-primary" disabled>Geen acties mogelijk</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Factor uitbetalingen</h5>
                        @if($uitbetalingen)
                            <p class="card-text">Facturen waarvan het factoring bedrag is uitbetaald.</p>
                            <a href="{{ route('factor-betalingen')}}" class="btn btn-primary">{{ $uitbetalingen }} eerste
                                betalingen</a>
                        @else
                            <p class="card-text">Er zijn nog geen betalingen geweest.</p>
                            <button class="btn btn-primary" disabled>Geen acties mogelijk</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Restbetalingen</h5>
                        @if($resterend)
                            <p class="card-text">Facturen die inmiddels betaald zijn door de opdrachtgever. De
                                resterende 37% (minus G-rekeningbetaling) van het factuurbedrag kan worden
                                uitbetaald.</p>
                            <a href="{{ route('restant-betaling')}}" class="btn btn-primary">{{ $resterend }}
                                restantbetalingen</a>
                        @else
                            <p class="card-text">Alle restbetalingen op factoringfacturen zijn bij.</p>
                            <button class="btn btn-primary" disabled>Geen acties mogelijk</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection
