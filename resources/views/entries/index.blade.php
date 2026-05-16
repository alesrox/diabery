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
            /* Usamos variables de Bootstrap para que cambie de color automáticamente */
            background-color: var(--bs-card-bg); 
            border-radius: 15px;
        }

        @media (min-width: 768px) {
            .app-container {
                margin-top: 20px;
                border-radius: 15px;
                min-height: auto;
            }
        }

        .entry-card { 
            /* Se adapta al color del borde según el tema de Bootstrap */
            border: 1px solid var(--bs-border-color); 
            border-radius: 16px; 
            transition: all 0.2s;
            background-color: var(--bs-card-bg);
        }

        .entry-card:hover {
            background-color: var(--bs-tertiary-bg);
        }

        .status-badge-container {
            width: 38px;
            height: 38px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .text-orange { color: #fd7e14 !important; }

        .glucose-box { 
            min-width: 45px; 
            text-align: center; 
        }

        .label-muted { 
            font-size: 0.65rem; 
            text-transform: uppercase; 
            color: #adb5bd; 
            font-weight: 700; 
        }

        /* Ajustes para pantallas mini */
        @media (max-width: 380px) {
            .card-body { padding: 10px !important; }
            .ms-3 { margin-left: 8px !important; }
            .ps-3 { padding-left: 8px !important; }
            .glucose-box { min-width: 35px; }
            .status-badge-container { width: 30px; margin-right: 10px !important; }
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
                    <div class="card-body d-flex align-items-center p-3">
                        
                        <div class="status-badge-container me-3 {{ $statusBg }}" style="width: 45px; height: 45px; border-radius: 12px;">
                            <i class="bi {{ $statusIcon }} {{ $iconColor }} fs-4"></i>
                        </div>

                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-baseline">
                                <span class="fw-bold text-capitalize text-truncate me-1 text-body">
                                    {{ __($entry->meal_type->value) }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ \Carbon\Carbon::parse($entry->entry_at, 'UTC')
                                        ->setTimezone(request()->cookie('timezone', 'Europe/Madrid'))
                                        ->isoFormat('D [de] MMMM, H:mm') }}
                                </small>
                            </div>
                            <div class="small text-muted">
                                <strong>{{ $entry->total_carbs_sum }}g</strong> CH · 
                                <span class="text-primary fw-bold">
                                    {{ number_format((float)$entry->meal_bolus + (float)$entry->correction_bolus, 1) }}u
                                </span>
                            </div>
                        </div>

                        <div class="ms-3 d-flex align-items-center border-start ps-3">
                            <div class="glucose-box me-3">
                                <div class="fw-bold fs-5 text-body">{{ $entry->glucose_pre }}</div>
                                <div class="label-muted">PRE</div>
                            </div>

                            <div class="glucose-box">
                                @if($hasPost)
                                    <div class="fw-bold fs-5 {{ $postColor }}">{{ $entry->glucose_post }}</div>
                                    <div class="label-muted">POST</div>
                                @elseif($isLate)
                                    <span class="badge bg-warning text-dark py-1 px-2" style="font-size: 0.65rem;">{{ __('MEASURE NOW!') }}</span>
                                @else
                                    <span class="badge rounded-pill bg-body text-primary border border-primary-subtle py-1 px-2" style="font-size: 0.6rem;">{{ __('MEASURE') }}</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-5">
                <p class="text-muted small">{{ __('No entries today') }}</p>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>