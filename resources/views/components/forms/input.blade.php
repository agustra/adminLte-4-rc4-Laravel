@props([
    'label',
    'name',
    'id',
    'type',
    'useIcon' => false,
    'useEmoji' => false,
    'emoji' => '',
    'icon' => '',
    'value' => null,
    'placeholder' => '',
    'isReadOnly' => false,
    'appendButton' => null,
    'helpText' => '',
])

<div class="form-group">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="input-group input-group-sm">
        @if ($useIcon || $useEmoji)
            <span class="input-group-text" id="icon-{{ $id }}">
                @if ($useIcon)
                    <i class="{{ $icon }}"></i>
                @elseif ($useEmoji)
                    <span style="font-size: 16px; display: flex; align-items: center;">{{ $emoji }}</span>
                @endif
            </span>
        @endif
        <input class="form-control form-control-sm {{ $name }}_validat {{ $attributes->get('class') ?? '' }}"
            type="{{ $type ?? 'text' }}" placeholder="{{ $placeholder }}" name="{{ $name }}"
            id="{{ $id }}" data-name="{{ $name }}" onkeypress="{{ $onkeypress ?? '' }}"
            onkeyup="{{ $onkeyup ?? '' }}" value="{{ $value }}" {{ $isReadOnly ? 'readonly' : '' }}>
        @if ($appendButton)
            {!! $appendButton !!}
        @endif
        {{ $slot ?? '' }}
        <div class="invalid-feedback"></div>
    </div>
    @if ($helpText)
        <small class="text-muted">{{ $helpText }}</small>
    @endif
</div>



<!-- Cara mengunakan -->
{{-- @component('components.forms.input', [
    'label' => 'Phone',
    'name' => 'phone',
    'id' => 'phone',
    'useIcon' => false,
    'useEmoji' => true,
    'emoji' => '☎️',
    'value' => old('phone', $results->phone ?? ''),
    'isReadOnly' => true,
])
                    @endcomponent --}}


{{-- <x-input 
    label="Nama"
    name="name"
    id="name"
    type="text"
    :useIcon="false"
    :useEmoji="false"
    emoji=""
    value="{{ old('name', $results->name ?? '') }}"
    :isReadOnly="true"
/> --}}
