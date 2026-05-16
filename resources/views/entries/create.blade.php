<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('New Entry') }} - Diabery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: var(--bs-body-bg); }
        .app-container { max-width: 80%; margin: auto; padding: 0 0 10px 0; background-color: var(--bs-card-bg); border-radius: 15px; }
        .food-card { border-left: 4px solid #0d6efd; background-color: var(--bs-card-bg); border-top: 1px solid var(--bs-border-color); border-right: 1px solid var(--bs-border-color); border-bottom: 1px solid var(--bs-border-color); }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm mt-md-4">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0 text-body">{{ __('New Entry') }}</h2>
        </div>

        <form action="{{ route('entries.store') }}" method="POST" id="entryForm">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Date and Time') }}</label>
                    <input type="datetime-local" name="entry_at" class="form-control form-control-lg" 
                        value="{{ \Carbon\Carbon::now('Europe/Madrid')->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Time of day') }}</label>
                    <select name="meal_type" class="form-select form-select-lg">
                        @foreach(\App\Enums\MealType::cases() as $type)
                            <option value="{{ $type->value }}">{{ ucfirst(__($type->value)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Pre-meal Glucose') }}</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="glucose_pre" class="form-control" placeholder="100">
                        <span class="input-group-text">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-bold text-primary small">{{ __('Meal Insulin') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="meal_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label fw-bold text-danger small">{{ __('Correction Insulin') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="correction_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-0 bg-body-tertiary">
                <div class="card-body">
                    <label class="form-label fw-bold text-body"><i class="bi bi-search me-1"></i> {{ __('Add Foods') }}</label>
                    <div class="row g-2">
                        <div class="col-8">
                            <select id="foodSelector" class="form-select">
                                <option value="">{{ __('Select a food...') }}</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}" data-carbs="{{ $food->carbs_100g }}">{{ $food->name }} ({{ $food->carbs_100g }}g/100g)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" id="foodWeight" class="form-control" placeholder="{{ __('Gramos') }}">
                                <button type="button" onclick="addFoodToList()" class="btn btn-primary"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold mb-3 text-body">{{ __('Selected food:') }}</h6>
                <div id="selectedFoodsList" class="list-group mb-3">
                    <p class="text-muted small text-center py-3" id="emptyMessage">{{ __('No foods added yet.') }}</p>
                </div>
                
                <div class="d-flex justify-content-between align-items-center p-3 bg-dark text-white rounded shadow-sm mb-4">
                    <div>
                        <div class="small opacity-75">{{ __('Total Carbohydrates') }}</div>
                        <span class="fs-4 fw-bold"><span id="totalCarbsLabel">0</span>g</span>
                        <input type="hidden" name="total_carbs_sum" id="total_carbs_sum" value="0">
                    </div>
                    <div class="text-end">
                        <div class="small opacity-75">{{ __('Total Insulin') }}</div>
                        <span class="fs-4 fw-bold text-info"><span id="totalInsulinDisplay">0.0</span>u</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-body-secondary small">{{ __('Notes or details') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Ej: Comida fuera de casa, poca actividad...') }}"></textarea>
            </div>

            <div class="d-grid pb-5">
                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow">
                    {{ __('Log and Calculate Insulin') }} <i class="bi bi-calculator ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
        
        const calculatedCarbs = (weight * carbs100g) / 100;

        const item = document.createElement('div');
        // Agregada la clase text-body para que se lea perfectamente en modo oscuro al inyectar desde JS
        item.className = "list-group-item d-flex justify-content-between align-items-center food-card mb-2 shadow-sm rounded text-body";
        item.innerHTML = `
            <div>
                <strong class="text-capitalize">${foodName}</strong><br>
                <small class="text-body-secondary">${weight}g | ${calculatedCarbs.toFixed(1)}g CH</small>
                <input type="hidden" name="foods[${foodId}][weight_grams]" value="${weight}">
                <input type="hidden" name="foods[${foodId}][calculated_carbs]" value="${calculatedCarbs.toFixed(1)}">
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger" onclick="this.parentElement.remove(); updateTotals();">
                <i class="bi bi-x-circle-fill fs-5"></i>
            </button>
        `;

        if (emptyMsg) emptyMsg.remove();
        list.appendChild(item);

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
            // El mensaje inyectado por JS dinámico también hereda el sistema de traducción de Laravel de forma segura pasándole el string directo
            list.innerHTML = `<p class="text-muted small text-center py-3" id="emptyMessage">{{ __('No foods added yet.') }}</p>`;
        }
    }

    document.querySelector('input[name="meal_bolus"]').addEventListener('input', updateTotals);
    document.querySelector('input[name="correction_bolus"]').addEventListener('input', updateTotals);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>