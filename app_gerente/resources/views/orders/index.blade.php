<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Order Management</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <!-- Formulario para enviar órdenes -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Crear Orden</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="quantity">Cantidad</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar orden a la cocina</button>
                </form>
            </div>
        </div>

        <!-- Listado de órdenes -->
        <div class="card">
            <div class="card-header">
                <h2>Listado de Ordenes</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Status</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->status->name }}</td>
                                <td>{{ $order->created_at }}</td>
                                <td>
                                    @if($order->status->name == 'Despachada')
                                        <i class="fas fa-check text-success"></i>
                                    @elseif($order->status->name == 'En proceso')
                                        <i class="fas fa-clock text-warning"></i>
                                    @else
                                        <i class="fas fa-minus"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Ingredientes Disponibles</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ingrediente</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="ingredients-table-body">
                        <!-- Aquí se cargarán los ingredientes -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cargar los ingredientes disponibles
            fetchIngredients();
        });

        function fetchIngredients() {
            $.ajax({
                url: '{{ route('orders.getIngredients') }}',
                method: 'GET',
                success: function(response) {
                    const ingredientsTableBody = $('#ingredients-table-body');
                    ingredientsTableBody.empty();

                    response.forEach(ingredient => {
                        ingredientsTableBody.append(`
                            <tr>
                                <td>${ingredient.name}</td>
                                <td>${ingredient.quantity}</td>
                            </tr>
                        `);
                    });
                },
                error: function(error) {
                    console.error('Error fetching ingredients:', error);
                }
            });
        }
    </script>
</body>
</html>
