<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Dashboard') }} - Diabery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: var(--bs-body-bg); }
        
        .app-container { 
            max-width: 80%; 
            margin: 0 auto; 
            background-color: var(--bs-card-bg); 
            border-radius: 15px;
        }

        @media (max-width: 992px) {
            .app-container { max-width: 95%; }
        }

        @media (min-width: 768px) {
            .app-container {
                margin-top: 20px;
                border-radius: 15px;
                min-height: auto;
            }
        }

        .entry-card { 
            border: 1px solid var(--bs-border-color); 
            border-radius: 16px; 
            transition: all 0.2s;
            background-color: var(--bs-card-bg);
        }

        .entry-card:hover {
            background-color: var(--bs-tertiary-bg);
        }

        .status-badge-container {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .text-orange { color: #fd7e14 !important; }

        .glucose-box { 
            min-width: 55px; 
            text-align: center; 
        }

        .label-muted { 
            font-size: 0.65rem; 
            text-transform: uppercase; 
            color: #adb5bd; 
            font-weight: 700; 
        }

        .linea-movil {
            border-bottom: 1px solid var(--bs-border-color);
        }

        @media (max-width: 575.98px) {
            .btn-filtrar-movil {
                width: 100%;
            }
        }

        @media (min-width: 576px) {
            .linea-movil {
                border-bottom: none !important;
            }
        }
    </style>
</head>
<body>

@include('components.navbar')
<div class="app-container shadow-sm pb-5">
    <div class="p-3 p-md-4">
        @if(session('init'))
        <a href="{{ route('settings.edit') }}" class="text-decoration-none">
            <div class="alert alert-info alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>
                    {{ session('init') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </a>
        @endif
        
        <div class="row g-2 mb-4">
            <div class="col-6">
                <div class="p-3 bg-body-tertiary rounded-3 text-center border">
                    <div class="label-muted mb-1">{{ __('Carb Ratio') }}</div>
                    <span class="fw-bold fs-5 text-primary">{{ auth()->user()->carb_insulin_ratio }}</span>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-body-tertiary rounded-3 text-center border">
                    <div class="label-muted mb-1">{{ __('Sensitivity') }}</div>
                    <span class="fw-bold fs-5 text-primary">{{ auth()->user()->insulin_sensitivity_factor }}</span>
                </div>
            </div>
        </div>

        <div class="card bg-body-tertiary border border-light-subtle rounded-3 mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('dashboard') }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-4">
                            <label class="label-muted mb-1 d-block" for="filter_date">{{ __('Date') }}</label>
                            <input type="date" name="date" id="filter_date" class="form-control form-control-sm bg-body" value="{{ request('date') }}">
                        </div>
                        
                        <div class="col-12 col-sm-5">
                            <label class="label-muted mb-1 d-block" for="filter_food">{{ __('Select Food') }}</label>
                            <select name="food" id="filter_food" class="form-select form-select-sm bg-body">
                                <option value="">{{ __('All foods') }}</option>
                                @foreach($userFoods as $food)
                                    <option value="{{ $food->id }}" {{ request('food') == $food->id ? 'selected' : '' }}>
                                        {{ $food->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-3 d-flex gap-1 justify-content-end">
                            @if(request()->filled('date') || request()->filled('food'))
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary flex-grow-1 flex-sm-grow-0" title="{{ __('Clear filters') }}">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                            <button type="submit" class="btn btn-sm btn-primary btn-filtrar-movil flex-grow-1 flex-sm-grow-0">
                                <i class="bi bi-funnel-fill me-1"></i>{{ __('Filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h5 class="fw-bold mb-3 px-1">{{ __("Dashboard's entries") }}</h5>
        
        @forelse($entries as $entry)
            @php
                $hasPost = !is_null($entry->glucose_post);
                $postColor = 'text-muted'; 
                
                $entryTime = \Carbon\Carbon::parse($entry->entry_at, 'UTC')->setTimezone('Europe/Madrid');
                $now = \Carbon\Carbon::now('Europe/Madrid');
                $minutesSinceEntry = $entryTime->diffInMinutes($now);

                $isLate = !$hasPost && ($minutesSinceEntry >= 150); 
                $isPending = !$hasPost && ($minutesSinceEntry < 150);

                if ($isLate) {
                    $statusBg = 'bg-warning'; 
                    $statusIcon = 'bi-exclamation-triangle-fill';
                    $iconColor = 'text-white'; 
                    $cardBorder = 'border-warning shadow-sm';
                } elseif ($isPending) {
                    $statusBg = 'bg-primary-subtle';
                    $statusIcon = 'bi-clock-fill';
                    $iconColor = 'text-primary';
                    $cardBorder = 'border-light-subtle';
                } else {
                    $statusBg = 'bg-success-subtle';
                    $statusIcon = 'bi-check-circle-fill';
                    $iconColor = 'text-success';
                    $cardBorder = 'border-light-subtle';

                    $postValue = $entry->glucose_post;

                    if ($hasPost) {
                        if ($postValue < 70) {
                            $postColor = 'text-danger'; 
                        } elseif ($postValue <= 180) {
                            $postColor = 'text-success';  
                        } elseif ($postValue <= 249) {
                            $postColor = 'text-warning';
                        } else {
                            $postColor = 'text-orange';
                        }
                    }
                }
            @endphp

            <a href="{{ route('entries.edit', $entry) }}" class="text-decoration-none text-dark d-block mb-3">
                <div class="card entry-card {{ $cardBorder }}" style="border-width: 2px;">
                    <div class="card-body p-3">
                        
                        <div class="d-flex flex-column d-sm-flex flex-sm-row align-items-start align-items-sm-center">
                            
                            <div class="d-flex align-items-center mb-3 mb-sm-0 me-sm-3">
                                <div class="status-badge-container me-3 {{ $statusBg }}">
                                    <i class="bi {{ $statusIcon }} {{ $iconColor }} fs-4"></i>
                                </div>
                                
                                <div class="overflow-hidden">
                                    <span class="fw-bold text-capitalize text-truncate text-body fs-5 fs-sm-6 d-block lh-sm">
                                        {{ __($entry->meal_type->value) }}
                                    </span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem; white-space: nowrap;">
                                        {{ \Carbon\Carbon::parse($entry->entry_at, 'UTC')
                                            ->setTimezone(request()->cookie('timezone', 'Europe/Madrid'))
                                            ->isoFormat('D [de] MMMM, H:mm') }}
                                    </small>
                                </div>
                            </div>

                            <div class="mb-3 mb-sm-0 me-sm-auto ps-0 ps-sm-2 w-100 w-sm-auto pb-2 pb-sm-0 linea-movil">
                                <div class="label-muted d-block d-sm-none mb-1">{{ __('Nutrients & Insulin') }}</div>
                                <div class="fs-6 text-muted">
                                    <strong class="text-body">{{ $entry->total_carbs_sum }}g</strong> CH · 
                                    <span class="text-primary fw-bold">
                                        {{ number_format((float)$entry->meal_bolus + (float)$entry->correction_bolus, 1) }}u
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end w-100 w-sm-auto pt-2 pt-sm-0 ps-sm-3 border-sm-start">
                                <div class="label-muted d-block d-sm-none">{{ __('Blood Glucose') }}</div>
                                
                                <div class="d-flex align-items-center">
                                    <div class="glucose-box me-4 me-sm-3">
                                        <div class="fw-bold fs-4 fs-sm-5 text-body">{{ $entry->glucose_pre }}</div>
                                        <div class="label-muted">PRE</div>
                                    </div>

                                    <div class="glucose-box">
                                        @if($hasPost)
                                            <div class="fw-bold fs-4 fs-sm-5 {{ $postColor }}">{{ $entry->glucose_post }}</div>
                                            <div class="label-muted">POST</div>
                                        @elseif($isLate)
                                            <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 0.65rem;">{{ __('MEASURE') }}</span>
                                        @else
                                            <span class="badge rounded-pill bg-body text-primary border border-primary-subtle py-1 px-2" style="font-size: 0.6rem;">{{ __('MEASURE') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div> </div>
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <p class="text-muted small">{{ __('No entries found with the selected filters') }}</p>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>