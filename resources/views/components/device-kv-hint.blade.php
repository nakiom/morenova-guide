@props([
    'compact' => false,
])

<aside {{ $attributes->class(['device-lcd', $compact ? 'device-lcd--compact' : null]) }}
       aria-label="{{ __('messages.device_kv_hint_label') }}">
    <div class="device-lcd__bezel">
        <span class="device-lcd__brand" aria-hidden="true">MoreNova</span>
        <div class="device-lcd__glass">
            <div class="device-lcd__screen">
                <p class="device-lcd__line">
                    <span class="device-lcd__kv">KV=1.0</span>
                    <span class="device-lcd__dash">-</span>
                    <span>{{ __('messages.device_kv_hint_protocol') }}</span>
                </p>
                <p class="device-lcd__line">
                    <span class="device-lcd__kv">KV=1.3</span>
                    <span class="device-lcd__dash">-</span>
                    <span>{{ __('messages.device_kv_hint_frequency') }}</span>
                    <span class="device-lcd__cursor" aria-hidden="true"></span>
                </p>
            </div>
        </div>
    </div>
</aside>
