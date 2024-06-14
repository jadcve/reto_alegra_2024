@extends('layouts.app')

@section('title', 'Registro de Compras')

@section('content')
    <h1 class="mb-4 text-center">Registro de Compras</h1>

    @if (isset($error))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h2>Compras Realizadas</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th>Cantidad Vendida</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseLogs as $log)
                            <tr>
                                <td>{{ $log['ingredient_name'] }}</td>
                                <td>{{ $log['quantity_sold'] }}</td>
                                <td>{{ $log['fecha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
