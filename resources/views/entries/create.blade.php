<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Entrada - Diabery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .app-container { max-width: 80%; margin: auto; padding: 0 0 10px 0; background: white; border-radius: 15px; }
        .food-card { border-left: 4px solid #0d6efd; }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Nueva Entrada</h2>
        </div>

        <form action="{{ route('entries.store') }}" method="POST" id="entryForm">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Momento del día</label>
                    <select name="meal_type" class="form-select form-select-lg">
                        @foreach(\App\Enums\MealType::cases() as $type)
                            <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Glucosa Pre-comida</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="glucose_pre" class="form-control" placeholder="100">
                        <span class="input-group-text">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-bold text-primary">Insulina Comida</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="meal_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold text-danger">Insulina Corrección</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="correction_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-0 bg-light">
                <div class="card-body">
                    <label class="form-label fw-bold"><i class="bi bi-search me-1"></i> Añadir Alimentos</label>
                    <div class="row g-2">
                        <div class="col-8">
                            <select id="foodSelector" class="form-select">
                                <option value="">Selecciona un alimento...</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}" data-carbs="{{ $food->carbs_100g }}">{{ $food->name }} ({{ $food->carbs_100g }}g/100g)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" id="foodWeight" class="form-control" placeholder="Gramos">
                                <button type="button" onclick="addFoodToList()" class="btn btn-primary"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-3">Comida seleccionada:</h6>
                <div id="selectedFoodsList" class="list-group mb-3">
                    <p class="text-muted small text-center py-3" id="emptyMessage">No hay alimentos añadidos todavía.</p>
                </div>
                
                <div class="d-flex justify-content-between align-items-center p-3 bg-dark text-white rounded shadow-sm mb-4">
                    <div>
                        <div class="small opacity-75">Total Carbohidratos</div>
                        <span class="fs-4 fw-bold"><span id="totalCarbsLabel">0</span>g</span>
                        <input type="hidden" name="total_carbs_sum" id="total_carbs_sum" value="0">
                    </div>
                    <div class="text-end">
                        <div class="small opacity-75">Insulina Total</div>
                        <span class="fs-4 fw-bold text-info"><span id="totalInsulinDisplay">0.0</span>u</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Notas o detalles</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Ej: Comida fuera de casa, poca actividad..."></textarea>
            </div>

            <div class="d-grid pb-5">
                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow">
                    Registrar y Calcular Insulina <i class="bi bi-calculator ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let totalCarbs = 0;

    function addFoodToList() {
        const selector = document.getElementById('foodSelector');
        const weightInput = document.getElementById('foodWeight');
        const list = document.getElementById('selectedFoodsList');
        const emptyMsg = document.getElementById('emptyMessage');

        if (!selector.value || !weightInput.value) return;

        const foodId = selector.value;
        const foodName = selector.options[selector.selectedIndex].text.split('(')[0];
        const carbs100g = parseFloat(selector.options[selector.selectedIndex].dataset.carbs);
        const weight = parseFloat(weightInput.value);
        
        // Cálculo de CH: (Gramos * Carbohidratos) / 100
        const calculatedCarbs = (weight * carbs100g) / 100;

        // Crear elemento en la lista
        const item = document.createElement('div');
        item.className = "list-group-item d-flex justify-content-between align-items-center food-card mb-2 shadow-sm rounded";
        item.innerHTML = `
            <div>
                <strong class="text-capitalize">${foodName}</strong><br>
                <small class="text-muted">${weight}g | ${calculatedCarbs.toFixed(1)}g CH</small>
                <input type="hidden" name="foods[${foodId}][weight_grams]" value="${weight}">
                <input type="hidden" name="foods[${foodId}][calculated_carbs]" value="${calculatedCarbs.toFixed(1)}">
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger" onclick="this.parentElement.remove(); updateTotals();">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </button>
        `;

        if (emptyMsg) emptyMsg.remove();
        list.appendChild(item);

        // Resetear inputs
        selector.value = "";
        weightInput.value = "";
        
        updateTotals();
    }

    function updateTotals() {
        const list = document.getElementById('selectedFoodsList');
        const carbInputs = list.querySelectorAll('input[name*="calculated_carbs"]');
        let totalCarbs = 0;
        carbInputs.forEach(input => {
            totalCarbs += parseFloat(input.value);
        });
        document.getElementById('totalCarbsLabel').innerText = totalCarbs.toFixed(1);
        document.getElementById('total_carbs_sum').value = totalCarbs.toFixed(1);

        const mealBolus = parseFloat(document.querySelector('input[name="meal_bolus"]').value) || 0;
        const correctionBolus = parseFloat(document.querySelector('input[name="correction_bolus"]').value) || 0;
        const totalInsulin = mealBolus + correctionBolus;
        
        document.getElementById('totalInsulinDisplay').innerText = totalInsulin.toFixed(1);

        if (carbInputs.length === 0 && !document.getElementById('emptyMessage')) {
            list.innerHTML = '<p class="text-muted small text-center py-3" id="emptyMessage">No hay alimentos añadidos todavía.</p>';
        }
    }

    document.querySelector('input[name="meal_bolus"]').addEventListener('input', updateTotals);
    document.querySelector('input[name="correction_bolus"]').addEventListener('input', updateTotals);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>