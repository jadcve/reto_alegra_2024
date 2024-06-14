<div class="card mb-4">
    <div class="card-header text-white" style="background-size: cover; background-position: center;">
        <h2 class="text-white">Crear Orden</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="quantity" class="col-form-label">Cantidad</label>
                </div>
                <div class="col-auto">
                    <input type="number" name="quantity" id="quantity" class="form-control" style="width: 100px;" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </div>
        </form>
    </div>
</div>
