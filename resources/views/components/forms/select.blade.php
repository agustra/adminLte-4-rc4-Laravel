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
    $selectedValue = $value ?? ($multiple ? [] : '');
    $selectedOptions = [];

    if ($multiple && is_iterable($value)) {
        foreach ($value as $val) {
            $text = $options[$val] ?? $val;
            $selectedOptions[] = ['id' => $val, 'text' => $text];
        }
    }
@endphp

<div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>
    <div class="input-group">
        @if ($useIcon || $useEmoji)
            <div class="input-group-prepend">
                <span class="input-group-text">
                    @if ($useIcon)
                        <i class="{{ $icon }}"></i>
                    @elseif ($useEmoji)
                        <span style="font-size: 15px; line-height: 1;">{{ $emoji }}</span>
                    @endif
                </span>
            </div>
        @endif

        <div class="flex-grow-1">
            <select class="form-select form-select-sm {{ $name }}_validat {{ $class }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}" id="{{ $id }}"
                data-name="{{ $id }}" data-placeholder="Pilih {{ $label }}"
                {{ $multiple ? 'multiple' : '' }} data-selected-id="{{ !$multiple ? $value : '' }}"
                data-selected-text="{{ !$multiple && isset($options[$value]) ? $options[$value] : '' }}"
                data-selected-options='@json($multiple ? collect($value)->map(fn($v) => ['id' => $v, 'text' => $options[$v] ?? $v]) : [])'>

                <option value="" disabled {{ empty($value) ? 'selected' : '' }}>-- Pilih {{ $label }} --
                </option>
                @foreach ($options as $key => $text)
                    <option value="{{ $key }}"
                        {{ ($multiple && is_array($value) && in_array($key, $value)) || (!$multiple && $key == $value)
                            ? 'selected'
                            : '' }}>
                        {{ $text }}
                    </option>
                @endforeach
            </select>


            <div class="invalid-feedback">
                <span class="text-danger error-text {{ $id }}_error {{ $classError }}"
                    name="{{ $id }}"></span>
            </div>
        </div>
    </div>
</div>

{{-- @component('components.forms.select', [
    'label' => 'Jabatan',
    'id' => 'jabatan',
    'name' => 'jabatan',
    'useIcon' => false,
    'useEmoji' => true,
    'emoji' => '🧑‍✈️',
    'class' => 'jabatan-select',
    'options' => optional($results->jabatan)->id ? [optional($results->jabatan)->id => optional($results->jabatan)->name] : [],
    'value' => old('jabatan', optional($results->jabatan)->id ?? ''),
    'multiple' => false,
])
                    @endcomponent --}}



{{-- <div class="form-group">
    <label for="{{ $id }}">{{ $label }}</label>
    <div class="input-group">
        @if ($useIcon || $useEmoji)
            <div class="input-group-prepend">
                <span class="input-group-text">
                    @if ($useIcon)
                        <i class="{{ $icon }}"></i>
                    @elseif ($useEmoji)
                        <span style="font-size: 15px; line-height: 1;">{{ $emoji }}</span>
                    @endif
                </span>
            </div>
        @endif
        <div class="flex-grow-1">
            <select class="form-select form-select-sm {{ $name }}_validat {{ $class ?? '' }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}" id="{{ $id }}"
                data-name="{{ $id }}" {{ $multiple ? 'multiple' : '' }}
                data-placeholder="Pilih {{ $label }}">
                <option {{ (!$multiple && empty($value)) ? 'selected' : '' }} disabled>-- Pilih {{ $label }} --</option>
                @foreach ($options as $key => $text)
                    <option value="{{ $key }}"
                        {{ (is_array($value) && in_array($key, $value)) || $value == $key ? 'selected' : '' }}>
                        {{ $text }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                <span class="text-danger error-text {{ $id }}_error {{ $classError ?? '' }}"
                    name="{{ $id }}"></span>
            </div>
        </div>
    </div>
</div> --}}


<!-- cara menggunakan -->
{{-- @component('components.forms.select-1', [
    'label' => 'Assign Role',
    'id' => 'role',
    'name' => 'role[]',
    'useIcon' => false,
    'useEmoji' => true,
    'emoji' => '💵',
    'class' => 'role-select',
    'options' => $rolesSelect,
    'value' => old('role', $user->roles ? $user->roles->pluck('id')->toArray() : []),
    'multiple' => true,
])
            @endcomponent --}}
