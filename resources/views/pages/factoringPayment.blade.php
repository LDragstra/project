@extends('layouts.app')

@section('content')
    <h1 class="lead mt-4">Uit te betalen facturen</h1>
    <table class="table table-sm table-striped mt-4">
        <thead class="">
        <tr>
            <th scope="col"></th>
            <th scope="col">Factuur</th>
            <th scope="col">Klant</th>
            <th scope="col">Factuurbedrag</th>
            <th scope="col">Omschrijving</th>
            <th scope="col">Selecteer</th>
            <th scope="col">Te betalen</th>
        </tr>
        </thead>
        <tbody>
        @foreach($uitTeKerenFacturen as $factuur)
            @php
                $unique = $factuur->getUniqueNumber($factuur->getFactuur->getProject->soort)
            @endphp
            <tr id="row-{{$factuur->id}}">
                <td><button class="btn btn-sm btn-outline-danger" onclick="deleteInitial({{$factuur->id}})">Verwijder</button></td>
                <td scope="row"><a class="mb-4" target="_blank"
                                   href="{{ route('showFactuur', $factuur->getFactuur->id) }}">{{ $factuur->getFactuur->nummer }}</a>
                </td>
                <td>{{ $factuur->getFactuur->getProject->soort }}
                    - {{ ($factuur->getFactuur->getProject->getClient) ? $factuur->getFactuur->getProject->getClient->bedrijfsnaam : 'Geen klant gekoppeld' }}</td>
                <td>€{{number_format($factuur->getFactuur->bedrag+$factuur->getFactuur->btw, 2, ',', '.')}}</td>
                <td>{{ $unique }}</td>
                <td><input type="checkbox" class="checkbox_class" name="checkboxes[]"></td>
                <td width="250px">
                    <button
                        onclick="initialPayment({{$factuur->id}}, {{number_format(($factuur->getFactuur->bedrag+$factuur->getFactuur->btw) * $percentage, 2, '.', '')}}, '{{ $unique }}')"
                        id="{{$factuur->id}}" class="btn btn-sm btn-secondary count">
                        €{{number_format(($factuur->getFactuur->bedrag+$factuur->getFactuur->btw) * $percentage, 2, ',', '')}}</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="box shadow-lg text-center">
            <p>Uit te betalen</p><p class="amount">€ 0,00</p>
    </div>



@endsection
