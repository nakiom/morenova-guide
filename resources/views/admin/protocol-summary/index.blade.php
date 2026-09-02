<x-app-layout>
    <div class="container py-5 protocol-summary">
        @php
            $thLines = static function (string $text): string {
                $parts = preg_split('/\s+/u', trim($text)) ?: [];

                return implode('<br>', array_map('e', $parts));
            };
        @endphp
        <div class="protocol-summary__hero mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                <div>
                    <p class="protocol-summary__eyebrow mb-2">{{ __('messages.protocol_catalog') }}</p>
                    <h1 class="h2 fw-bold text-dark-blue mb-2">{{ __('messages.protocol_summary') }}</h1>
                    <p class="text-muted mb-3">{{ __('messages.protocol_summary_hint') }}</p>
                    <div class="d-flex flex-wrap gap-2 protocol-summary__actions">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>{{ __('messages.print') }}
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
                        </a>
                    </div>
                </div>
                <x-device-kv-hint compact class="protocol-summary__lcd flex-shrink-0" />
            </div>

            <nav class="protocol-summary__jump mt-4" aria-label="{{ __('messages.protocol_summary') }}">
                @foreach($categories as $category)
                    @php
                        $catKey = str_replace('-', '_', $category->slug);
                        $catLabel = __('messages.'.$catKey);
                    @endphp
                    <a href="#category-{{ $category->slug }}" class="protocol-summary__jump-link">
                        {{ $catLabel }}
                        <span class="protocol-summary__jump-count">{{ $category->pathologies->count() }}</span>
                    </a>
                @endforeach
                <a href="#protocol-calculator" class="protocol-summary__jump-link">
                    <i class="fas fa-calculator"></i>
                    {{ __('messages.protocol_calculator') }}
                </a>
            </nav>
        </div>

        @forelse($categories as $category)
            @php
                $catKey = str_replace('-', '_', $category->slug);
                $catLabel = __('messages.'.$catKey);
            @endphp
            <section id="category-{{ $category->slug }}" class="protocol-summary__section mb-5">
                <div class="protocol-summary__section-head">
                    <h2 class="h4 fw-bold text-dark-blue mb-0">{{ $catLabel }}</h2>
                    <span class="protocol-summary__section-meta">
                        {{ trans_choice('messages.protocol_summary_count', $category->pathologies->count(), ['count' => $category->pathologies->count()]) }}
                    </span>
                </div>

                <div class="table-responsive protocol-summary__table-wrap">
                    <table class="table protocol-summary__table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="protocol-summary__th--name">{{ __('messages.pathology') }}</th>
                                <th scope="col">{!! $thLines(__('messages.param_transducer')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_sessions')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_sessions_frequency')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_session_duration')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_applications_per_session')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_shots_per_application')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_shots_per_session')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_energy')) !!}</th>
                                <th scope="col">{!! $thLines(__('messages.param_frequency')) !!}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->pathologies as $pathology)
                                @php
                                    $name = $pathology->translations->firstWhere('locale', $locale)?->name
                                        ?? $pathology->translations->first()?->name
                                        ?? $pathology->slug;
                                    $txUrl = $pathology->transducerImageUrl();
                                @endphp
                                <tr>
                                    <td class="protocol-summary__pathology">
                                        <div class="d-flex align-items-center gap-2">
                                            <x-pathology-icon :pathology="$pathology" class="pathology-icon-img pathology-icon-img--table" size="2rem" />
                                            <div>
                                                <div class="fw-semibold text-dark-blue">{{ $name }}</div>
                                                <div class="small text-muted text-uppercase">{{ strtoupper($pathology->slug) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pathology->transducer)
                                            <span class="protocol-summary__tx">
                                                @if($txUrl)
                                                    <img src="{{ $txUrl }}" alt="{{ $pathology->transducer }}" class="protocol-summary__tx-img">
                                                @endif
                                                <span class="protocol-summary__tx-code">{{ $pathology->transducer }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $pathology->sessions_count ?: '—' }}</td>
                                    <td>{{ $pathology->sessions_frequency ?: '—' }}</td>
                                    <td>
                                        @if($pathology->session_duration_minutes)
                                            {{ $pathology->session_duration_minutes }} <span class="text-muted">{{ __('messages.unit_minutes') }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $pathology->applications_per_session ?: '—' }}</td>
                                    <td>
                                        @if($pathology->shots_per_application)
                                            {{ number_format($pathology->shots_per_application, 0, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        @if($pathology->shots_per_session)
                                            {{ number_format($pathology->shots_per_session, 0, ',', '.') }}
                                            <span class="d-block small text-muted fw-normal">{{ __('messages.unit_shots') }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($pathology->energy_kv !== null)
                                            {{ number_format((float) $pathology->energy_kv, 2, ',', '.') }} <span class="text-muted">kV</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($pathology->frequency_hz !== null)
                                            {{ number_format((float) $pathology->frequency_hz, 2, ',', '.') }} <span class="text-muted">Hz</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-muted text-center py-4">{{ __('messages.no_pathologies') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @empty
            <div class="alert alert-light border">{{ __('messages.no_pathologies') }}</div>
        @endforelse

        <section id="protocol-calculator" class="protocol-calc mb-0 mt-2" data-protocols='@json($calculatorProtocols)'>
            <div class="protocol-calc__head">
                <div>
                    <h2 class="h4 fw-bold text-dark-blue mb-1">
                        <i class="fas fa-calculator me-2 text-primary"></i>{{ __('messages.protocol_calculator') }}
                    </h2>
                    <p class="text-muted mb-0 small">{{ __('messages.protocol_calculator_hint') }}</p>
                </div>
            </div>

            <div class="protocol-calc__inputs row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="calc-shots">{{ __('messages.calc_transducer_shots') }}</label>
                    <input type="number" id="calc-shots" class="form-control form-control-lg" min="1" step="1" value="500000" placeholder="500000" inputmode="numeric">
                    <small class="text-muted">{{ __('messages.calc_transducer_shots_hint') }}</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="calc-cost">{{ __('messages.calc_transducer_cost') }}</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">$</span>
                        <input type="number" id="calc-cost" class="form-control" min="0" step="0.01" placeholder="0" inputmode="decimal">
                    </div>
                    <small class="text-muted">{{ __('messages.calc_transducer_cost_hint') }}</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="calc-price">{{ __('messages.calc_treatment_price') }}</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text">$</span>
                        <input type="number" id="calc-price" class="form-control" min="0" step="0.01" placeholder="0" inputmode="decimal">
                    </div>
                    <small class="text-muted">{{ __('messages.calc_treatment_price_hint') }}</small>
                </div>
            </div>

            <div class="protocol-calc__kpis row g-3 mt-1" hidden>
                <div class="col-sm-6 col-lg-3">
                    <div class="protocol-calc__kpi">
                        <div class="protocol-calc__kpi-label">{{ __('messages.calc_cost_per_shot') }}</div>
                        <div class="protocol-calc__kpi-value" data-kpi="cost-per-shot">—</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="protocol-calc__kpi">
                        <div class="protocol-calc__kpi-label">{{ __('messages.calc_treatments_possible') }}</div>
                        <div class="protocol-calc__kpi-value" data-kpi="treatments-range">—</div>
                        <div class="protocol-calc__kpi-note">{{ __('messages.calc_across_protocols') }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="protocol-calc__kpi protocol-calc__kpi--accent">
                        <div class="protocol-calc__kpi-label">{{ __('messages.calc_avg_cost_treatment') }}</div>
                        <div class="protocol-calc__kpi-value" data-kpi="avg-cost">—</div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="protocol-calc__kpi protocol-calc__kpi--profit">
                        <div class="protocol-calc__kpi-label">{{ __('messages.calc_avg_profit') }}</div>
                        <div class="protocol-calc__kpi-value" data-kpi="avg-profit">—</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive protocol-summary__table-wrap mt-4">
                <table class="table protocol-summary__table protocol-calc__table mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="protocol-summary__th--name">{{ __('messages.pathology') }}</th>
                            <th scope="col">{!! $thLines(__('messages.param_transducer')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.param_shots_per_session')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.param_sessions')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_cost_per_session')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_cost_per_treatment')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_treatment_price')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_profit')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_margin')) !!}</th>
                            <th scope="col">{!! $thLines(__('messages.calc_treatments_from_tx')) !!}</th>
                        </tr>
                    </thead>
                    <tbody data-calc-body>
                        <tr>
                            <td colspan="10" class="text-muted text-center py-4">{{ __('messages.calc_enter_values') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @php
        $calcLocale = str_replace('_', '-', app()->getLocale());
        if (! in_array($calcLocale, ['es', 'en', 'pt', 'zh'], true)) {
            $calcLocale = 'es';
        }
        $jsLocale = match ($calcLocale) {
            'en' => 'en-US',
            'pt' => 'pt-BR',
            'zh' => 'zh-CN',
            default => 'es-AR',
        };
    @endphp
    <script>
        (() => {
            const root = document.getElementById('protocol-calculator');
            if (!root) return;

            const protocols = JSON.parse(root.dataset.protocols || '[]');
            const shotsInput = document.getElementById('calc-shots');
            const costInput = document.getElementById('calc-cost');
            const priceInput = document.getElementById('calc-price');
            const body = root.querySelector('[data-calc-body]');
            const kpis = root.querySelector('.protocol-calc__kpis');
            const locale = @json($jsLocale);
            const emptyMsg = @json(__('messages.calc_enter_values'));
            const dash = '—';

            const money = (n) => {
                if (!Number.isFinite(n)) return dash;
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: 'USD',
                    maximumFractionDigits: n < 1 ? 4 : 2,
                }).format(n);
            };

            const moneyCompact = (n) => {
                if (!Number.isFinite(n)) return dash;
                return new Intl.NumberFormat(locale, {
                    style: 'currency',
                    currency: 'USD',
                    maximumFractionDigits: 2,
                }).format(n);
            };

            const intFmt = (n) => {
                if (!Number.isFinite(n)) return dash;
                return new Intl.NumberFormat(locale, { maximumFractionDigits: 0 }).format(Math.floor(n));
            };

            const rangeMoney = (min, max) => {
                if (!Number.isFinite(min) || !Number.isFinite(max)) return dash;
                if (Math.abs(min - max) < 0.005) return moneyCompact(min);
                return `${moneyCompact(min)} – ${moneyCompact(max)}`;
            };

            const rangeInt = (min, max) => {
                if (!Number.isFinite(min) || !Number.isFinite(max)) return dash;
                if (min === max) return intFmt(min);
                return `${intFmt(min)} – ${intFmt(max)}`;
            };

            const read = () => ({
                shots: Number(shotsInput.value),
                cost: Number(costInput.value),
                price: priceInput.value === '' ? null : Number(priceInput.value),
            });

            const render = () => {
                const { shots, cost, price } = read();
                const ready = shots > 0 && cost >= 0 && costInput.value !== '';

                if (!ready) {
                    kpis.hidden = true;
                    body.innerHTML = `<tr><td colspan="10" class="text-muted text-center py-4">${emptyMsg}</td></tr>`;
                    return;
                }

                const costPerShot = cost / shots;
                const rows = [];
                let costSum = 0;
                let profitSum = 0;
                let counted = 0;
                let txMin = Infinity;
                let txMax = -Infinity;

                protocols.forEach((p) => {
                    const sps = p.shots_per_session;
                    const sessions = p.sessions;
                    const costSession = sps ? costPerShot * sps : null;
                    let costTreatMin = null;
                    let costTreatMax = null;
                    let treatmentsMin = null;
                    let treatmentsMax = null;

                    if (costSession !== null && sessions) {
                        costTreatMin = costSession * sessions.min;
                        costTreatMax = costSession * sessions.max;
                        const shotsTreatMin = sps * sessions.min;
                        const shotsTreatMax = sps * sessions.max;
                        treatmentsMax = shotsTreatMin > 0 ? shots / shotsTreatMin : null;
                        treatmentsMin = shotsTreatMax > 0 ? shots / shotsTreatMax : null;
                        if (treatmentsMin !== null) txMin = Math.min(txMin, treatmentsMin);
                        if (treatmentsMax !== null) txMax = Math.max(txMax, treatmentsMax);
                    }

                    const costMid = costTreatMin !== null
                        ? (costTreatMin + costTreatMax) / 2
                        : null;
                    const profitMin = price !== null && costTreatMax !== null ? price - costTreatMax : null;
                    const profitMax = price !== null && costTreatMin !== null ? price - costTreatMin : null;
                    const profitMid = price !== null && costMid !== null ? price - costMid : null;
                    const margin = price && price > 0 && profitMid !== null
                        ? (profitMid / price) * 100
                        : null;

                    if (costMid !== null) {
                        costSum += costMid;
                        counted += 1;
                        if (profitMid !== null) profitSum += profitMid;
                    }

                    const profitClass = profitMid === null
                        ? ''
                        : (profitMid >= 0 ? 'is-profit' : 'is-loss');

                    rows.push(`
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark-blue">${escapeHtml(p.name)}</div>
                                <div class="small text-muted">${escapeHtml(p.category)} · ${escapeHtml((p.slug || '').toUpperCase())}</div>
                            </td>
                            <td>${p.transducer ? `<span class="protocol-summary__tx-code">${escapeHtml(p.transducer)}</span>` : dash}</td>
                            <td>${sps ? intFmt(sps) : dash}</td>
                            <td>${p.sessions_label ? escapeHtml(p.sessions_label) : dash}</td>
                            <td class="fw-semibold">${costSession !== null ? moneyCompact(costSession) : dash}</td>
                            <td class="fw-semibold">${rangeMoney(costTreatMin, costTreatMax)}</td>
                            <td>${price !== null ? moneyCompact(price) : dash}</td>
                            <td class="fw-bold ${profitClass}">${rangeMoney(profitMin, profitMax)}</td>
                            <td>${margin !== null ? `${margin.toFixed(0)}%` : dash}</td>
                            <td>${rangeInt(treatmentsMin, treatmentsMax)}</td>
                        </tr>
                    `);
                });

                body.innerHTML = rows.join('') || `<tr><td colspan="10" class="text-muted text-center py-4">${emptyMsg}</td></tr>`;

                kpis.hidden = false;
                root.querySelector('[data-kpi="cost-per-shot"]').textContent = money(costPerShot);
                root.querySelector('[data-kpi="treatments-range"]').textContent =
                    Number.isFinite(txMin) ? rangeInt(txMin, txMax) : dash;
                root.querySelector('[data-kpi="avg-cost"]').textContent =
                    counted ? moneyCompact(costSum / counted) : dash;
                root.querySelector('[data-kpi="avg-profit"]').textContent =
                    counted && price !== null ? moneyCompact(profitSum / counted) : dash;
            };

            const escapeHtml = (str) => String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');

            [shotsInput, costInput, priceInput].forEach((el) => {
                el.addEventListener('input', render);
            });

            const defaultShots = 500000;

            try {
                const saved = JSON.parse(localStorage.getItem('protocolCalc') || 'null');
                if (saved) {
                    shotsInput.value = saved.shots || defaultShots;
                    if (saved.cost != null && saved.cost !== '') costInput.value = saved.cost;
                    if (saved.price != null && saved.price !== '') priceInput.value = saved.price;
                } else {
                    shotsInput.value = defaultShots;
                }
            } catch (_) {
                shotsInput.value = defaultShots;
            }

            const persist = () => {
                const { shots, cost, price } = read();
                localStorage.setItem('protocolCalc', JSON.stringify({
                    shots: shotsInput.value,
                    cost: costInput.value,
                    price: priceInput.value,
                }));
            };

            [shotsInput, costInput, priceInput].forEach((el) => {
                el.addEventListener('change', persist);
                el.addEventListener('input', persist);
            });

            render();
        })();
    </script>
</x-app-layout>
