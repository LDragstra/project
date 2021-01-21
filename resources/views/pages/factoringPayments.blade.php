@extends('layouts.app')

@section('content')
    <h1 class="lead mt-4">Uitbetaalde facturen</h1>
    <table id="dataTable" class="table table-sm table-striped mt-4">
        <thead>
            <tr>
                <th scope="col">Factuur</th>
                <th scope="col">Klant</th>
                <th scope="col">Factuurbedrag</th>
                <th scope="col">Betaaldatum</th>
                <th scope="col">Omschrijving</th>
                <th scope="col">G-rekening</th>
                <th scope="col">Uitbetaald</th>
            </tr>
        </thead>
        <tbody>
        @foreach($uitbetalingen as $factor)
            <tr>
                <td scope="row"><a class="mb-4" target="_blank"
                                   href="{{ route('showFactuur', $factor->getFactuur->id) }}">{{ $factor->getFactuur->nummer }}</a></td>
                <td>{{ $factor->getFactuur->getProject->soort }} - {{ ($factor->getFactuur->getProject->getClient) ? $factor->getFactuur->getProject->getClient->bedrijfsnaam : 'Geen klant gekoppeld' }}</td>
                <td>€{{number_format($factor->getFactuur->bedrag+$factor->getFactuur->btw, 2, ',', '.')}}</td>
                <td>{{ $factor->datum->format('d-m-Y') }}</td>
                <td>{{ $factor->nummer }}</td>
                <td>€{{number_format($factor->getFactuur->grekeningSaldo, 2, ',', '.')}}</td>
                <td width="250px">
                        €{{number_format($factor->initieel, 2, ',', '.')}}
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    {{ $uitbetalingen->links() }}

@endsection
