@props([
    'label',
    'id',
    'name',
    'options' => [],
    'value' => null,
    'multiple' => false,
    'class' => '',
    'classError' => '',
    'useIcon' => false,
    'useEmoji' => false,
    'icon' => '',
    'emoji' => '',
])

@php
    // Gunakan old() jika ada error validasi, jika tidak ambil default value
    $selectedValue = old($name, $value);
    if ($multiple && !is_array($selectedValue)) {
        $selectedValue = $selectedValue ? explode(',', $selectedValue) : [];
    }
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>

    <div class="input-group input-group-sm">
        {{-- Icon / Emoji --}}
        @if ($useIcon || $useEmoji)
            <span class="input-group-text">
                @if ($useIcon)
                    <i class="{{ $icon }}"></i>
                @elseif ($useEmoji)
                    <span style="font-size: 16px; display: flex; align-items: center;">{{ $emoji }}</span>
                @endif
            </span>
        @endif

        <select id="{{ $id }}" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            class="form-select form-select-sm {{ $name }}_validat {{ $errors->has($name) ? 'is-invalid' : '' }} {{ $class }}"
            data-selected="{{ is_array($selectedValue) ? implode(',', $selectedValue) : $selectedValue }}"
            data-placeholder="Pilih {{ $label }}" {{ $multiple ? 'multiple' : '' }}>
            <option value="">
                {{ $multiple ? '-- Pilih beberapa ' . $label . ' --' : '-- Pilih ' . $label . ' --' }}
            </option>
            @foreach ($options as $key => $text)
                <option value="{{ $key }}"
                    @if ($multiple && is_array($selectedValue) && in_array($key, $selectedValue)) selected
                    @elseif (!$multiple && (string) $key === $selectedValue)
                        selected @endif>
                    {{ $text }}
                </option>
            @endforeach
        </select>
        
        {{-- Error feedback --}}
        <div class="invalid-feedback" @if ($errors->has($name)) style="display:block;" @endif>
            <span class="text-danger error-text {{ $id }}_error {{ $classError }}"></span>
        </div>
    </div>
</div>


{{-- <x-forms.tomSelect label="Merek" id="merek_id" name="merek_id" :useEmoji="true" emoji="🙎‍♂️"
                        value="{{ old('merek_id', $data->merek_id ?? '') }}" /> --}}
