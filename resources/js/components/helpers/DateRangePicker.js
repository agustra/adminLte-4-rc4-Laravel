/**
 * DateRangePicker Component
 * Komponen untuk memilih rentang tanggal dengan Bootstrap dropdown dan Flatpickr
 * Auto-load semua dependencies yang diperlukan
 */
export default class DateRangePicker {
    constructor(container, options = {}) {
        this.container =
            typeof container === "string"
                ? document.querySelector(container)
                : container;
        this.options = {
            onDateSelect: options.onDateSelect || this.defaultCallback,
            buttonText: options.buttonText || "Pilih Filter",
            buttonClass:
                options.buttonClass ||
                "btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center",
            buttonStyle: options.buttonStyle || "min-width: 150px;",
            ...options,
        };

        this.loadDependencies().then(() => {
            this.init();
        });
    }

    async loadDependencies() {
        // Load CSS
        if (!document.querySelector('link[href*="flatpickr"]')) {
            await this.loadCSS(
                "https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
            );
        }

        // Load JavaScript dependencies
        const scripts = [
            {
                url: "https://cdn.jsdelivr.net/npm/flatpickr",
                check: () => window.flatpickr,
            },
            {
                url: "https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js",
                check: () => window.flatpickr?.l10ns?.id,
            },
            {
                url: "https://cdn.jsdelivr.net/npm/dayjs@1.10.8/dayjs.min.js",
                check: () => window.dayjs,
            },
            {
                url: "https://cdn.jsdelivr.net/npm/dayjs@1.10.8/plugin/advancedFormat.js",
                check: () => window.dayjs_plugin_advancedFormat,
            },
            {
                url: "https://cdn.jsdelivr.net/npm/dayjs@1.10.8/plugin/customParseFormat.js",
                check: () => window.dayjs_plugin_customParseFormat,
            },
        ];

        for (const script of scripts) {
            if (!script.check()) {
                await this.loadScript(script.url);
            }
        }
    }

    loadCSS(href) {
        return new Promise((resolve) => {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = href;
            link.onload = resolve;
            document.head.appendChild(link);
        });
    }

    loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement("script");
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    init() {
        this.render();
        this.initializeFlatpickr();
        this.bindEvents();
    }

    render() {
        const html = `
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex justify-content-between align-items-center"
                        type="button" id="daterange-btn-${this.generateId()}" data-bs-toggle="dropdown"
                        style="${this.options.buttonStyle}">
                    <span class="text-small">
                        <i class="far fa-calendar-alt me-2"></i>
                        <span class="date-range-text">${
                            this.options.buttonText
                        }</span>
                    </span>
                </button>

                <ul class="dropdown-menu small" style="min-width: 150px;">
                    <li><h6 class="dropdown-header small">Pilihan Cepat</h6></li>
                    <li><a class="dropdown-item small" href="#" data-range="today">
                        <i class="fas fa-sun me-2"></i>Hari Ini
                    </a></li>
                    <li><a class="dropdown-item small" href="#" data-range="yesterday">
                        <i class="fas fa-moon me-2"></i>Kemarin
                    </a></li>
                    <li><a class="dropdown-item small" href="#" data-range="last7">
                        <i class="fas fa-calendar-week me-2"></i>7 Hari Terakhir
                    </a></li>
                    <li><a class="dropdown-item small" href="#" data-range="last30">
                        <i class="fas fa-calendar me-2"></i>30 Hari Terakhir
                    </a></li>
                    <li><a class="dropdown-item small" href="#" data-range="thisMonth">
                        <i class="fas fa-calendar-alt me-2"></i>Bulan Ini
                    </a></li>
                    <li><a class="dropdown-item small" href="#" data-range="lastMonth">
                        <i class="fas fa-calendar-day me-2"></i>Bulan Lalu
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item small custom-range-btn" href="#">
                        <i class="fas fa-calendar-plus me-2"></i>Rentang Kustom
                    </a></li>
                    <li><a class="dropdown-item small text-danger clear-btn" href="#">
                        <i class="fas fa-times me-2"></i>Reset Filter
                    </a></li>
                </ul>
            </div>

            <input type="text" class="flatpickr-hidden"
                style="position: absolute; left: -9999px; opacity: 0; width: 1px; height: 1px;">
        `;

        this.container.innerHTML = html;

        // Cache DOM elements
        this.button = this.container.querySelector('[id^="daterange-btn-"]');
        this.flatpickrInput = this.container.querySelector(".flatpickr-hidden");
        this.quickOptions = this.container.querySelectorAll("[data-range]");
        this.customRangeBtn = this.container.querySelector(".custom-range-btn");
        this.clearBtn = this.container.querySelector(".clear-btn");
    }

    initializeFlatpickr() {
        // Extend DayJS
        dayjs.extend(dayjs_plugin_advancedFormat);
        dayjs.extend(dayjs_plugin_customParseFormat);

        this.flatpickrInstance = flatpickr(this.flatpickrInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "id",
            clickOpens: false,
            positionElement: this.button,
            onClose: (selectedDates) => {
                if (selectedDates.length === 2) {
                    this.handleDateSelect(
                        dayjs(selectedDates[0]),
                        dayjs(selectedDates[1])
                    );
                }
            },
        });
    }

    bindEvents() {
        // Quick range options
        this.quickOptions.forEach((option) => {
            option.addEventListener("click", (e) => {
                e.preventDefault();
                const range = this.calculateDateRange(
                    option.getAttribute("data-range")
                );

                this.flatpickrInstance.setDate([
                    range.start.toDate(),
                    range.end.toDate(),
                ]);
                this.handleDateSelect(range.start, range.end);
                this.closeDropdown();
            });
        });

        // Custom range handler
        this.customRangeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            this.closeDropdown();
            this.flatpickrInstance.open();
        });

        // Clear/Reset handler
        this.clearBtn.addEventListener("click", (e) => {
            e.preventDefault();
            this.clearDateRange();
            this.closeDropdown();
        });
    }

    calculateDateRange(rangeType) {
        const today = dayjs();
        let start, end;

        switch (rangeType) {
            case "today":
                start = today;
                end = today;
                break;
            case "yesterday":
                start = today.subtract(1, "day");
                end = today.subtract(1, "day");
                break;
            case "last7":
                start = today.subtract(6, "days");
                end = today;
                break;
            case "last30":
                start = today.subtract(29, "days");
                end = today;
                break;
            case "thisMonth":
                start = today.startOf("month");
                end = today.endOf("month");
                break;
            case "lastMonth":
                start = today.subtract(1, "month").startOf("month");
                end = today.subtract(1, "month").endOf("month");
                break;
        }

        return { start, end };
    }

    handleDateSelect(startDate, endDate) {
        const dateRange = {
            start: startDate.format("YYYY-MM-DD"),
            end: endDate.format("YYYY-MM-DD"),
            formatted: `${startDate.format("D MMM YYYY")} - ${endDate.format(
                "D MMM YYYY"
            )}`,
            startDate: startDate,
            endDate: endDate,
        };

        this.options.onDateSelect(dateRange);
    }

    closeDropdown() {
        const dropdown = bootstrap.Dropdown.getInstance(this.button);
        if (dropdown) dropdown.hide();
    }

    defaultCallback(dateRange) {
        console.log("Selected date range:", dateRange);
    }

    generateId() {
        // Use cryptographically secure random number generator
        if (typeof crypto !== "undefined" && crypto.getRandomValues) {
            const array = new Uint32Array(1);
            crypto.getRandomValues(array);
            return array[0].toString(36).substr(2, 9);
        }
        // Fallback for older browsers
        return Math.random().toString(36).substr(2, 9);
    }

    clearDateRange() {
        this.flatpickrInstance.clear();
        const dateRangeText = this.container.querySelector(".date-range-text");
        if (dateRangeText) {
            dateRangeText.textContent = this.options.buttonText;
        }
        this.options.onDateSelect(null);
    }

    // Public methods
    destroy() {
        if (this.flatpickrInstance) {
            this.flatpickrInstance.destroy();
        }
        this.container.innerHTML = "";
    }

    setDateRange(startDate, endDate) {
        const start = dayjs(startDate);
        const end = dayjs(endDate);
        this.flatpickrInstance.setDate([start.toDate(), end.toDate()]);
        this.handleDateSelect(start, end);
    }
}

// Export sebagai default ES6 module
// Untuk backward compatibility, tetap expose ke window
if (typeof window !== "undefined") {
    window.DateRangePicker = DateRangePicker;
}
