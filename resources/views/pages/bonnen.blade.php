@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body ss-card mb-4">
                    <h1>Bonnen omzetten naar facturen</h1>

                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <hr>
                    <div class="row mb-4 mt-4">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <h2>Data uit VAP</h2>
                        </div>
                    </div>
                    @foreach($bonnen as $bon)
                    @php
                    $klant = $bon->getKlantFromBon;
                    if($klant->snelStart){
                            $klantId = $klant->snelStart;
                        } else {
                            $klantId = $bon->getKlantFromApi($relaties, $klant->bedrijfsnaam);
                        }
                    @endphp

                    <div class="row mb-4 mt-4" id="row-{{$bon->id}}">
                        <div class="col-6">
                            <div class="float-left custom-control custom-switch">
                                <input @if($bon->bon >= 1000) checked @endif type="checkbox" class="custom-control-input" id="fl-{{$bon->id}}">
                                <label class="custom-control-label" for="fl-{{$bon->id}}">Factor deze factuur</label>
                            </div>
                            <div class="float-right custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" @if($bon->getKlantFromBon->merge ==
                                1) checked @endif id="pdf-{{$bon->id}}">
                                <label class="custom-control-label" for="pdf-{{$bon->id}}">PDF samenvoegen</label>
                            </div>
                            <input type="date" class="form-control" name="date-{{$bon->id}}" id="date-{{$bon->id}}"
                                value="<?php echo date('Y-m-d');?>">
                            <select id="sjabloon-{{$bon->id}}" class="form-control" name="ssSjabloon" id="ssSjabloon">
                                <option value="">Kies sjabloon voor de factuur</option>
                                @foreach($sjablonen as $sjabloon)
                                @if(!$sjabloon['nonactief'])
                                <option value="{{$sjabloon['id']}}">{{$sjabloon['omschrijving']}}</option>
                                @endif
                                @endforeach
                            </select>
                            <select id="select-{{$bon->id}}" class="form-control" name="ssKlant" id="ssKlant">
                                <option value="">Kies klant om te factureren</option>
                                @foreach($relaties as $relatie)
                                @if($relatie['id'] === $klantId)
                                <option value="{{$relatie['id']}}" selected>{{$relatie['naam']}}</option>
                                @else
                                <option value="{{$relatie['id']}}">{{$relatie['naam']}}</option>
                                @endif
                                @endforeach
                            </select>
                            <select id="artikel-{{$bon->id}}" class="form-control" name="ssKlant" id="ssKlant">
                                <option value="">Kies artikel</option>
                                @foreach($artikelen as $artikel)
                                @if(session('company')['naam'] == 'Firestop Doorvoeringen' && $artikel['artikelcode'] ==
                                32)
                                <option value="{{$artikel['id']}}" selected>{{$artikel['artikelcode']}} -
                                    {{$artikel['omschrijving']}}</option>
                                @elseif(session('company')['naam'] == 'Firestop B.V.' && $artikel['artikelcode'] == 16)
                                <option value="{{$artikel['id']}}" selected>{{$artikel['artikelcode']}} -
                                    {{$artikel['omschrijving']}}</option>
                                @elseif(session('company')['naam'] == 'Firestop Onderhoud' && $artikel['artikelcode'] ==
                                1007)
                                <option value="{{$artikel['id']}}" selected>{{$artikel['artikelcode']}} -
                                    {{$artikel['omschrijving']}}</option>
                                @else
                                <option value="{{$artikel['id']}}">{{$artikel['artikelcode']}} -
                                    {{$artikel['omschrijving']}}</option>
                                @endif
                                @endforeach
                            </select>
                            <textarea class="form-control mb-4"
                                id="toelichting-{{$bon->id}}">{{ str_replace('â‚¬ ', '€', $bon->toelichtingbon) }}</textarea>
                            <button type="button" id="submit-{{$bon->id}}" onclick="makeInvoice({{$bon->id}})"
                                class="btn btn-secondary">
                                Verkoopfactuur
                                maken van
                                bovenstaande bon
                            </button>
                        </div>
                        <div class="col-6 ss-card-left">
                            @if($bon->eindgebruiker)
                            <div class="alert alert-danger" role="alert">
                                Let op: eindgebruiker
                            </div>
                            @endif
                            <input type="hidden" name="project-{{$bon->id}}" id="project-{{$bon->id}}"
                                value="{{$bon->getProject->id}}">
                            <input type="hidden" name="week-{{$bon->id}}" id="week-{{$bon->id}}" value="{{$bon->week}}">
                            <p id="projectNaam-{{$bon->id}}">{{ $bon->getProject->projectnaam }} </p>
                            <p>{{ $bon->getKlantFromBon->bedrijfsnaam }} </p>
                            <p>€<input id="bedrag-{{$bon->id}}" value="{{$bon->bon}}"></p>
                            <p><input type="number" name="termijn-{{$bon->id}}" id="termijn-{{$bon->id}}"
                                    value="{{$bon->getProject->betaaltermijn}}"></p>
                            <p><strong> Week {{ $bon->week }} </strong> </p>

                            <?php if($bon->map && $bon->map != 'Geen bon nodig'):?>
                            <p><a target="_blank" href="https://www.fqr.nl{{$bon->map}}">Bon.pdf</a> </p>
                            <?php endif;?>

                            <button class="btn btn-danger" onclick="deleteBon({{$bon->id}})" id="delete-{{$bon->id}}"
                                title="Reeds gefactureerd" aria-label="Delete">
                                <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                            </a>
                        </div>
                    </div>
                    <hr id="hr-{{$bon->id}}">
                    @endforeach
                </div>
                {{$bonnen->links()}}
            </div>
        </div>
    </div>
</div>

@endsection
