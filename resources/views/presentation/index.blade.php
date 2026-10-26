<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('presentation.meta_title') }}</title>
    <link rel="icon" type="image/x-icon" href="https://direxgroup.com/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/presentation.css', 'resources/js/presentation.js'])
</head>
<body class="preso-body">
    <div class="preso-shell">
        <div class="preso-wave" aria-hidden="true"></div>

        <header class="preso-topbar">
            <a class="preso-brand" href="{{ route('home') }}">
                <img src="{{ asset('img/morenova-string.png') }}" alt="MoreNova">
                <span class="preso-brand-tag">Guide</span>
            </a>
            <div class="preso-top-actions">
                <nav class="preso-lang-switch" aria-label="Language">
                    @foreach (['es', 'en', 'pt'] as $code)
                        <a href="{{ route('locale.switch', $code) }}"
                           class="preso-lang-btn {{ app()->getLocale() === $code ? 'is-active' : '' }}"
                           @if(app()->getLocale() === $code) aria-current="true" @endif>
                            {{ strtoupper($code) }}
                        </a>
                    @endforeach
                </nav>
                <span class="preso-hint" data-preso-counter>1 / 9</span>
                <button type="button" class="preso-icon-btn" data-preso-fullscreen title="{{ __('presentation.fullscreen_title') }}" aria-label="{{ __('presentation.fullscreen') }}">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </header>

        <main class="preso-deck" data-preso-deck>
            {{-- 1. Hero --}}
            <section class="preso-slide preso-slide-hero is-active" data-preso-slide>
                <div class="preso-hero">
                    <div class="preso-hero-copy">
                        <div class="preso-kicker">{{ __('presentation.slide1.kicker') }}</div>
                        <h1 class="preso-title">{!! __('presentation.slide1.title') !!}</h1>
                        <p class="preso-lead">{{ __('presentation.slide1.lead') }}</p>
                       
                    </div>

                    <div class="preso-hero-media preso-product preso-product-video">
                        <video autoplay muted loop playsinline>
                            <source src="{{ asset('videos/Morenova-pro-turnable-3D-homepage-1.mp4') }}" type="video/mp4">
                        </video>
                    </div>

                    <div class="preso-hero-highlights">
                        <div class="preso-highlight-item">
                            <i class="fas fa-laptop-mobile"></i>
                            <span>{{ __('presentation.slide1.highlight_desktop') }}</span>
                        </div>
                        <div class="preso-highlight-item">
                            <i class="fas fa-book-medical"></i>
                            <span>{{ __('presentation.slide1.highlight_protocols') }}</span>
                        </div>
                        <div class="preso-highlight-group">
                            <div class="preso-highlight-head">
                                <i class="fas fa-link"></i>
                                <span>{{ __('presentation.slide1.highlight_connection_title') }}</span>
                            </div>
                            <ul>
                                <li>{{ __('presentation.slide1.highlight_connection_us') }}</li>
                                <li>{{ __('presentation.slide1.highlight_connection_patient') }}</li>
                            </ul>
                        </div>
                        <div class="preso-highlight-group">
                            <div class="preso-highlight-head">
                                <i class="fas fa-database"></i>
                                <span>{{ __('presentation.slide1.highlight_org_title') }}</span>
                            </div>
                            <ul>
                                <li>{{ __('presentation.slide1.highlight_org_appointments') }}</li>
                                <li>{{ __('presentation.slide1.highlight_org_stats') }}</li>
                                <li>{{ __('presentation.slide1.highlight_org_charts') }}</li>
                            </ul>
                        </div>
                    </div>
                     <div class="preso-cta-row">
                        <button type="button" class="preso-btn preso-btn-primary" data-preso-next>
                            {{ __('presentation.slide1.cta_start') }} <i class="fas fa-arrow-right"></i>
                        </button>
                        <a href="{{ route('login') }}" class="preso-btn preso-btn-ghost">{{ __('presentation.slide1.cta_login') }}</a>
                    </div>
                </div>
            </section>

            {{-- 2. Audiencia --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-kicker">{{ __('presentation.slide2.kicker') }}</div>
                <h2 class="preso-title">{!! __('presentation.slide2.title') !!}</h2>
                <p class="preso-lead">{{ __('presentation.slide2.lead') }}</p>
                <div class="preso-grid-3">
                    <article class="preso-feature">
                        <i class="fas fa-user-md"></i>
                        <h3>{{ __('presentation.slide2.doctors_title') }}</h3>
                        <p>{{ __('presentation.slide2.doctors_text') }}</p>
                    </article>
                    <article class="preso-feature">
                        <i class="fas fa-hospital"></i>
                        <h3>{{ __('presentation.slide2.centers_title') }}</h3>
                        <p>{{ __('presentation.slide2.centers_text') }}</p>
                    </article>
                    <article class="preso-feature">
                        <i class="fas fa-user-shield"></i>
                        <h3>{{ __('presentation.slide2.admin_title') }}</h3>
                        <p>{{ __('presentation.slide2.admin_text') }}</p>
                    </article>
                </div>
            </section>

            {{-- 3. Protocolos --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-grid-2">
                    <div>
                        <div class="preso-kicker">{{ __('presentation.slide3.kicker') }}</div>
                        <h2 class="preso-title">{!! __('presentation.slide3.title') !!}</h2>
                        <p class="preso-lead">{{ __('presentation.slide3.lead') }}</p>
                        <div class="preso-grid-3" style="grid-template-columns: 1fr 1fr; max-width: 28rem;">
                            <article class="preso-feature">
                                <i class="fas fa-sliders"></i>
                                <h3>{{ __('presentation.slide3.params_title') }}</h3>
                                <p>{{ __('presentation.slide3.params_text') }}</p>
                            </article>
                            <article class="preso-feature">
                                <i class="fas fa-clipboard-check"></i>
                                <h3>{{ __('presentation.slide3.checklist_title') }}</h3>
                                <p>{{ __('presentation.slide3.checklist_text') }}</p>
                            </article>
                        </div>
                    </div>
                    <div class="preso-device" aria-hidden="true">
                        <div class="preso-device-bar">
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                        </div>
                        <div class="preso-device-screen">
                            <div class="preso-ui-nav">
                                <strong>ED · Li50M</strong>
                                <span class="preso-ui-chip">{{ __('presentation.slide3.ui_protocol') }}</span>
                            </div>
                            <div class="preso-ui-cards">
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide3.ui_sessions') }}</div><div class="value">6</div></div>
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide3.ui_shocks') }}</div><div class="value">3000</div></div>
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide3.ui_duration') }}</div><div class="value">20'</div></div>
                            </div>
                            <div class="preso-ui-list">
                                <div class="preso-ui-row"><span>{{ __('presentation.slide3.ui_transducer') }}</span><span class="ok">OK</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide3.ui_inclusion') }}</span><span class="ok">OK</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide3.ui_gel') }}</span><span class="ok">OK</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4. Pacientes compartidos --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-grid-2">
                    <div>
                        <div class="preso-kicker">{{ __('presentation.slide4.kicker') }}</div>
                        <h2 class="preso-title">{!! __('presentation.slide4.title') !!}</h2>
                        <p class="preso-lead">{{ __('presentation.slide4.lead') }}</p>
                        <div class="preso-stat-row">
                            <div class="preso-stat"><strong>1</strong><span>{{ __('presentation.slide4.stat1') }}</span></div>
                            <div class="preso-stat"><strong>N</strong><span>{{ __('presentation.slide4.stat2') }}</span></div>
                            <div class="preso-stat"><strong>0</strong><span>{{ __('presentation.slide4.stat3') }}</span></div>
                        </div>
                    </div>
                    <div class="preso-device" aria-hidden="true">
                        <div class="preso-device-bar">
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                        </div>
                        <div class="preso-device-screen">
                            <div class="preso-ui-nav">
                                <strong>{{ __('presentation.slide4.ui_title') }}</strong>
                                <span class="preso-ui-chip">{{ __('presentation.slide4.ui_center') }}</span>
                            </div>
                            <div class="preso-ui-list">
                                <div class="preso-ui-row">
                                    <span>Paciente A · ED</span>
                                    <span>Dra. Vega</span>
                                </div>
                                <div class="preso-ui-row">
                                    <span>Paciente B · IUE</span>
                                    <span>Dr. Soler</span>
                                </div>
                                <div class="preso-ui-row">
                                    <span>Paciente C · GSM</span>
                                    <span>Dra. Núñez</span>
                                </div>
                            </div>
                            <div class="preso-ui-cards" style="margin-top:0.6rem">
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide3.ui_sessions') }}</div><div class="value">4</div></div>
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide4.ui_next_visit') }}</div><div class="value">12/08</div></div>
                                <div class="preso-ui-card"><div class="label">{{ __('presentation.slide3.ui_shocks') }}</div><div class="value">12k</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 5. En el momento del tratamiento --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-grid-2">
                    <div>
                        <div class="preso-kicker">{{ __('presentation.slide5.kicker') }}</div>
                        <h2 class="preso-title">{!! __('presentation.slide5.title') !!}</h2>
                        <p class="preso-lead">{{ __('presentation.slide5.lead') }}</p>
                        <div class="preso-pill-list">
                            <span class="preso-pill">{{ __('presentation.slide5.pill_params') }}</span>
                            <span class="preso-pill">{{ __('presentation.slide5.pill_history') }}</span>
                            <span class="preso-pill">{{ __('presentation.slide5.pill_contact') }}</span>
                        </div>
                    </div>
                    <div class="preso-phone" aria-hidden="true">
                        <div class="preso-phone-notch"></div>
                        <div class="preso-phone-screen">
                            <h4>{{ __('presentation.slide5.ui_record') }}</h4>
                            <div class="preso-ui-card" style="margin-bottom:0.45rem">
                                <div class="label">{{ __('presentation.slide5.ui_energy') }}</div>
                                <div class="value">2.0 kV</div>
                            </div>
                            <div class="preso-ui-card" style="margin-bottom:0.45rem">
                                <div class="label">{{ __('presentation.slide5.ui_frequency') }}</div>
                                <div class="value">2.0 Hz</div>
                            </div>
                            <div class="preso-ui-card">
                                <div class="label">{{ __('presentation.slide5.ui_session_shocks') }}</div>
                                <div class="value">3000</div>
                            </div>
                            <div class="preso-ui-list" style="margin-top:0.55rem">
                                <div class="preso-ui-row"><span>IIEF pre</span><span class="ok">18</span></div>
                                <div class="preso-ui-row"><span>IIEF post</span><span class="ok">24</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 6. Cuestionarios email --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-grid-2">
                    <div>
                        <div class="preso-kicker">{{ __('presentation.slide6.kicker') }}</div>
                        <h2 class="preso-title">{!! __('presentation.slide6.title') !!}</h2>
                        <p class="preso-lead">{{ __('presentation.slide6.lead') }}</p>
                        <div class="preso-grid-3" style="grid-template-columns:1fr 1fr; max-width:30rem">
                            <article class="preso-feature">
                                <i class="fas fa-paper-plane"></i>
                                <h3>{{ __('presentation.slide6.send_title') }}</h3>
                                <p>{{ __('presentation.slide6.send_text') }}</p>
                            </article>
                            <article class="preso-feature">
                                <i class="fas fa-eye"></i>
                                <h3>{{ __('presentation.slide6.visited_title') }}</h3>
                                <p>{{ __('presentation.slide6.visited_text') }}</p>
                            </article>
                        </div>
                    </div>
                    <div class="preso-device" aria-hidden="true">
                        <div class="preso-device-bar">
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                        </div>
                        <div class="preso-device-screen">
                            <div class="preso-ui-nav">
                                <strong>{{ __('presentation.slide6.ui_invite') }}</strong>
                                <span class="preso-ui-chip">{{ __('presentation.slide6.ui_pre') }}</span>
                            </div>
                            <div class="preso-ui-list">
                                <div class="preso-ui-row"><span>IIEF-EF</span><span class="ok">{{ __('presentation.slide6.ui_included') }}</span></div>
                                <div class="preso-ui-row"><span>EHS</span><span class="ok">{{ __('presentation.slide6.ui_included') }}</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide6.ui_status') }}</span><span class="ok">{{ __('presentation.slide6.ui_visited') }}</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide6.ui_next_appointment') }}</span><span>27/08</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 7. Evolución --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-grid-2">
                    <div>
                        <div class="preso-kicker">{{ __('presentation.slide7.kicker') }}</div>
                        <h2 class="preso-title">{!! __('presentation.slide7.title') !!}</h2>
                        <p class="preso-lead">{{ __('presentation.slide7.lead') }}</p>
                        <div class="preso-pill-list">
                            <span class="preso-pill">{{ __('presentation.slide7.pill_scales') }}</span>
                            <span class="preso-pill">{{ __('presentation.slide7.pill_charts') }}</span>
                            <span class="preso-pill">{{ __('presentation.slide7.pill_reports') }}</span>
                        </div>
                    </div>
                    <div class="preso-device" aria-hidden="true">
                        <div class="preso-device-bar">
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                            <span class="preso-device-dot"></span>
                        </div>
                        <div class="preso-device-screen">
                            <div class="preso-ui-nav">
                                <strong>{{ __('presentation.slide7.ui_evolution') }}</strong>
                                <span class="preso-ui-chip">+6 pts</span>
                            </div>
                            <div class="preso-chart">
                                <div class="preso-bar"></div>
                                <div class="preso-bar"></div>
                                <div class="preso-bar"></div>
                                <div class="preso-bar"></div>
                                <div class="preso-bar"></div>
                            </div>
                            <div class="preso-ui-list">
                                <div class="preso-ui-row"><span>{{ __('presentation.slide7.ui_pre') }}</span><span>18</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide7.ui_post') }}</span><span class="ok">24</span></div>
                                <div class="preso-ui-row"><span>{{ __('presentation.slide7.ui_control') }}</span><span class="ok">25</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 8. Cadena de valor --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-kicker">{{ __('presentation.slide8.kicker') }}</div>
                <h2 class="preso-title">{!! __('presentation.slide8.title') !!}</h2>
                <p class="preso-lead">{{ __('presentation.slide8.lead') }}</p>
                <ol class="preso-chain">
                    <li class="preso-chain-step">
                        <span class="preso-chain-num">01</span>
                        <h3>{{ __('presentation.slide8.step1_title') }}</h3>
                        <p>{{ __('presentation.slide8.step1_text') }}</p>
                    </li>
                    <li class="preso-chain-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></li>
                    <li class="preso-chain-step">
                        <span class="preso-chain-num">02</span>
                        <h3>{{ __('presentation.slide8.step2_title') }}</h3>
                        <p>{{ __('presentation.slide8.step2_text') }}</p>
                    </li>
                    <li class="preso-chain-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></li>
                    <li class="preso-chain-step">
                        <span class="preso-chain-num">03</span>
                        <h3>{{ __('presentation.slide8.step3_title') }}</h3>
                        <p>{{ __('presentation.slide8.step3_text') }}</p>
                    </li>
                    <li class="preso-chain-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></li>
                    <li class="preso-chain-step">
                        <span class="preso-chain-num">04</span>
                        <h3>{{ __('presentation.slide8.step4_title') }}</h3>
                        <p>{{ __('presentation.slide8.step4_text') }}</p>
                    </li>
                </ol>
            </section>

            {{-- 9. Cierre --}}
            <section class="preso-slide" data-preso-slide>
                <div class="preso-closing">
                    <div class="preso-kicker">{{ __('presentation.slide9.kicker') }}</div>
                    <h2 class="preso-title">{!! __('presentation.slide9.title') !!}</h2>
                    <p class="preso-lead">{{ __('presentation.slide9.lead') }}</p>
                    <div class="preso-cta-row">
                        <a href="{{ route('home') }}" class="preso-btn preso-btn-primary">
                            {{ __('presentation.slide9.cta_home') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="preso-bottom">
            <div class="preso-progress" aria-hidden="true"><span data-preso-progress></span></div>
            <button type="button" class="preso-nav-btn preso-nav-left" data-preso-prev disabled>
                <i class="fas fa-arrow-left"></i> {{ __('presentation.nav_prev') }}
            </button>
            <div class="preso-dots" role="tablist" aria-label="{{ __('presentation.slides_label') }}">
                @for($i = 0; $i < 9; $i++)
                    <button type="button"
                            class="preso-dot {{ $i === 0 ? 'is-active' : '' }}"
                            data-preso-dot="{{ $i }}"
                            aria-label="{{ __('presentation.slide_goto', ['num' => $i + 1]) }}"
                            aria-current="{{ $i === 0 ? 'true' : 'false' }}"></button>
                @endfor
            </div>
            <button type="button" class="preso-nav-btn preso-nav-right" data-preso-next>
                {{ __('presentation.nav_next') }} <i class="fas fa-arrow-right"></i>
            </button>
        </footer>
    </div>
</body>
</html>
