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
        .app-container { max-width: 80%; margin: 20px auto; padding: 0 0 10px 0; background-color: var(--bs-card-bg); border-radius: 15px; }
        .food-card { border-left: 4px solid #198754; background-color: var(--bs-card-bg); border-top: 1px solid var(--bs-border-color); border-right: 1px solid var(--bs-border-color); border-bottom: 1px solid var(--bs-border-color); }
    </style>
</head>
<body>
@include('components.navbar')
<div class="app-container shadow-sm mt-md-4">
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0 text-body">{{ __('New Entry') }}</h2>
            <a href="{{ route('dashboard') }}" class="btn-close"></a>
        </div>

        <form action="{{ route('entries.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Date and Time') }}</label>
                    <input type="datetime-local" name="entry_at" class="form-control form-control-lg" 
                    value="{{ \Carbon\Carbon::now()->timezone(request()->cookie('timezone', 'Europe/Madrid'))->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Time of day') }}</label>
                    <select name="meal_type" class="form-select form-select-lg">
                        @foreach(\App\Enums\MealType::cases() as $type)
                            <option value="{{ $type->value }}">
                                {{ ucfirst(__($type->value)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-body-secondary small">{{ __('Pre-meal Glucose') }}</label>
                    <div class="input-group input-group-lg">
                        <input type="number" name="glucose_pre" class="form-control" placeholder="0">
                        <span class="input-group-text">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-6">
                    <label class="form-label fw-bold text-primary small">{{ __('Meal Insulin') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="meal_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-bold text-danger small">{{ __('Correction Insulin') }}</label>
                    <div class="input-group">
                        <input type="number" step="0.5" name="correction_bolus" class="form-control form-control-lg" placeholder="0.0">
                        <span class="input-group-text">u</span>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label fw-bold small text-body-secondary">{{ __('POST GLUCOSE') }}</label>
                    <div class="input-group">
                        <input type="number" name="glucose_post" class="form-control form-control-lg border-secondary" placeholder="{{ __('Pending') }}">
                        <span class="input-group-text">mg/dL</span>
                    </div>
                </div>
            </div>

            <div class="card mb-4 border-0 bg-body-tertiary">
                <div class="card-body">
                    <label class="form-label fw-bold small text-body-secondary"><i class="bi bi-search me-1"></i> {{ __('ADD FOODS') }}</label>
                    <div class="row g-2">
                        <div class="col-8">
                            <select id="foodSelector" class="form-select" onchange="updatePlaceholder()">
                                <option value="">{{ __('Select...') }}</option>
                                @foreach($foods as $food)
                                    <option value="{{ $food->id }}" 
                                        data-carbs="{{ $food->quantity }}" data-measure="{{ $food->measure_type }}">
                                        @if($food->measure_type == 'units')
                                            {{ $food->name }} ({{ $food->quantity }}g CH/{{ __('unit') }})
                                        @else
                                            {{ $food->name }} ({{ $food->quantity }}g CH/100g)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <input type="number" step="0.1" id="foodWeight" class="form-control" placeholder="g">
                                <button type="button" onclick="addFoodToList()" class="btn btn-primary"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="selectedFoodsList" class="list-group mb-4">
                <p class="text-muted small text-center py-3" id="emptyMessage">{{ __('No foods added yet.') }}</p>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 bg-dark text-white rounded shadow-sm mb-4">
                <div>
                    <div class="small opacity-75">{{ __('Total Carbs:') }}</div>
                    <span class="fs-4 fw-bold"><span id="totalCarbsLabel">0.0</span>g CH</span>
                    <input type="hidden" name="total_carbs_sum" id="total_carbs_sum" value="0">
                </div>
                <div class="text-end">
                    <div class="small opacity-75">{{ __('Total Insulin') }}</div>
                    <span class="fs-4 fw-bold text-info"><span id="totalInsulinDisplay">0.0</span>u</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-body-secondary small">{{ __('Notes or details') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Ej: Comida fuera de casa, poca actividad...') }}"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow">{{ __('Create Entry') }}</button>
        </form>
    </div>
</div>

<script>
// Modifica dinámicamente el placeholder del input según el tipo de medida
function updatePlaceholder() {
    const selector = document.getElementById('foodSelector');
    const weightInput = document.getElementById('foodWeight');
    
    if (!selector.value) {
        weightInput.placeholder = "g";
        return;
    }
    
    const measure = selector.options[selector.selectedIndex].dataset.measure;
    weightInput.placeholder = (measure === 'units') ? "uds" : "g";
}

function addFoodToList() {
    const selector = document.getElementById('foodSelector');
    const weightInput = document.getElementById('foodWeight');
    const list = document.getElementById('selectedFoodsList');

    if (!selector.value || !weightInput.value) return;

    const foodId = selector.value;
    const option = selector.options[selector.selectedIndex];
    const foodName = option.text.split('(')[0].trim();
    const carbsBase = parseFloat(option.dataset.carbs);
    
    const measureType = option.dataset.measure; 
    const quantity = parseFloat(weightInput.value);
    
    let calculatedCarbs = 0;
    let unitText = "";

    if (measureType === 'units') {
        calculatedCarbs = quantity * carbsBase;
        unitText = quantity + "u";
    } else {
        calculatedCarbs = (quantity * carbsBase) / 100;
        unitText = Math.round(quantity) + "g";
    }

    const emptyMsg = document.getElementById('emptyMessage');
    if (emptyMsg) emptyMsg.remove();

    const item = document.createElement('div');
    item.className = "list-group-item d-flex justify-content-between align-items-center food-card mb-2 shadow-sm rounded text-body";
    item.innerHTML = `
        <div>
            <strong class="text-capitalize">${foodName}</strong><br>
            <small class="text-body-secondary">${unitText} | ${calculatedCarbs.toFixed(1)}g CH</small>
            
            <input type="hidden" name="foods[${foodId}][quantity]" value="${quantity}">
            <input type="hidden" name="foods[${foodId}][measure_type]" value="${measureType}">
            <input type="hidden" name="foods[${foodId}][calculated_carbs]" value="${calculatedCarbs.toFixed(1)}">
        </div>
        <button type="button" class="btn btn-sm text-danger" onclick="this.parentElement.remove(); updateTotals();">
            <i class="bi bi-trash"></i>
        </button>
    `;
    
    list.appendChild(item);
    
    selector.value = ""; 
    weightInput.value = "";
    weightInput.placeholder = "g";
    
    updateTotals();
}

function updateTotals() {
    const list = document.getElementById('selectedFoodsList');
    const carbInputs = list.querySelectorAll('input[name*="calculated_carbs"]');
    
    let total = 0;
    carbInputs.forEach(input => {
        total += parseFloat(input.value);
    });
    
    document.getElementById('totalCarbsLabel').innerText = total.toFixed(1);
    document.getElementById('total_carbs_sum').value = total.toFixed(1);

    const mealBolus = parseFloat(document.querySelector('input[name="meal_bolus"]').value) || 0;
    const correctionBolus = parseFloat(document.querySelector('input[name="correction_bolus"]').value) || 0;
    const totalInsulin = mealBolus + correctionBolus;
    document.getElementById('totalInsulinDisplay').innerText = totalInsulin.toFixed(1);

    if (carbInputs.length === 0 && !document.getElementById('emptyMessage')) {
        list.innerHTML = `<p class="text-muted small text-center py-3" id="emptyMessage">{{ __('No foods added yet.') }}</p>`;
    }
}

document.querySelector('input[name="meal_bolus"]').addEventListener('input', updateTotals);
document.querySelector('input[name="correction_bolus"]').addEventListener('input', updateTotals);

document.addEventListener("DOMContentLoaded", updateTotals);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>