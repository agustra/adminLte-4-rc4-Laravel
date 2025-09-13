<style>
    .theme-switch-wrapper {
        background-color: #343a40;
        transition: all 0.3s ease;
    }

    .theme-label {
        font-size: 0.8rem;
        color: #fff;
    }

    .theme-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .theme-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #007bff;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #007bff;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
