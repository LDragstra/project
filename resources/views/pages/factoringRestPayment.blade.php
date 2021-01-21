@extends('layouts.app')

@section('content')
    <h1 class="lead mt-4">Restant uit te betalen facturen</h1>
    <table class="table table-sm table-striped mt-4">
        <thead class="">
        <tr>
            <th scope="col">Factuur</th>
            <th scope="col">Klant</th>
            <th scope="col">100%</th>
            <th scope="col">Initele betaling</th>
            <th scope="col">G-rekening</th>
            <th scope="col">Omschrijving</th>
            <th scope="col">Selecteer</th>
            <th scope="col">Te betalen</th>
        </tr>
        </thead>
        <tbody>
        @foreach($restUitkering as $factor)
            <tr>
                <td scope="row"><a class="mb-4" target="_blank"
                                   href="{{ route('showFactuur', $factor->getFactuur->id) }}">{{ $factor->getFactuur->nummer }}</a></td>
                <td>{{ $factor->getFactuur->getProject->soort }} - {{ ($factor->getFactuur->getProject->getClient) ? $factor->getFactuur->getProject->getClient->bedrijfsnaam : 'Geen klant gekoppeld' }}</td>
                <td>€{{number_format($factor->getFactuur->bedrag+$factor->getFactuur->btw, 2, ',', '.')}}</td>
                <td>€{{number_format($factor->initieel, 2, ',', '.')}}</td>
                <td>€{{number_format($factor->getFactuur->grekeningSaldo, 2, ',', '.')}}</td>
                <td>{{ $factor->nummer }}</td>
                <td><input type="checkbox" class="checkbox_class" name="checkboxes[]"></td>
                <td width="250px">
                    <button
                        onclick="restPayment({{$factor->id}},{{number_format(($factor->getFactuur->bedrag+$factor->getFactuur->btw - $factor->initieel) - $factor->getFactuur->grekeningSaldo, 2, '.', '')}})"
                        id="{{$factor->id}}" class="btn btn-sm btn-secondary count">
                        €{{number_format(($factor->getFactuur->bedrag + $factor->getFactuur->btw - $factor->initieel) - $factor->getFactuur->grekeningSaldo, 2, ',', '')}}</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="box shadow-lg text-center">
        <p>Uit te betalen</p><p class="amount">€ 0,00</p>
    </div>



@endsection
