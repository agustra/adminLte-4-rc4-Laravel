@props([
    'label',
    'name',
    'id',
    'useIcon' => false,
    'icon' => '',
    'useEmoji' => false,
    'emoji' => '',
    'value' => null,
    'isReadOnly' => false,
    'placeholder' => '',
    'class' => '',
    'onkeypress' => null,
    'onkeyup' => null,
])

<label for="{{ $id }}">{{ $label }}</label>
<div class="input-group input-group-sm">
    @if ($useIcon || $useEmoji)
        <span class="input-group-text textarea-icon-wrapper" id="icon-{{ $id }}">
            @if ($useIcon)
                <i class="{{ $icon }}"></i>
            @elseif ($useEmoji)
                <span>{{ $emoji }}</span>
            @endif
        </span>
    @endif
    <textarea class="form-control form-control-sm {{ $name }}_validat {{ $class ?? '' }}" name="{{ $name }}"
        id="{{ $id }}" data-name="{{ $name }}" rows="3" onkeypress="{{ $onkeypress ?? '' }}"
        onkeyup="{{ $onkeyup ?? '' }}" {{ $isReadOnly ? 'readonly' : '' }}>{{ $value }}</textarea>
    <div class="invalid-feedback"></div>
</div>
