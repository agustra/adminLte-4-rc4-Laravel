import AirDatepicker from "air-datepicker";
import "air-datepicker/air-datepicker.css";

export default function filterData(onFilterChange) {
    // console.log("✅ filterData.js loaded");

    // Inject CSS untuk Air Datepicker dark mode
    if (!document.getElementById("air-datepicker-dark-styles")) {
        const style = document.createElement("style");
        style.id = "air-datepicker-dark-styles";
        style.textContent = `
            .air-datepicker.air-datepicker-dark {
                background: #212529 !important;
                border: 1px solid #495057 !important;
                color: #f8f9fa !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--cell {
                color: #f8f9fa !important;
                background: transparent !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--cell:hover {
                background: #0d6efd !important;
                color: #ffffff !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--cell.-selected- {
                background: #0d6efd !important;
                color: #ffffff !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--nav {
                background: #212529 !important;
                border-bottom: 1px solid #495057 !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--nav-title,
            .air-datepicker.air-datepicker-dark .air-datepicker--nav-action {
                color: #f8f9fa !important;
            }
            .air-datepicker.air-datepicker-dark .air-datepicker--nav-action:hover {
                background: #495057 !important;
            }
        `;
        document.head.appendChild(style);
    }

    let filterContainer = document.getElementById("idFilterSingleDate");
    if (!filterContainer) {
        console.error("❌ Elemen #idFilterSingleDate tidak ditemukan!");
        return;
    }

    // Bersihkan elemen sebelum menambahkan
    filterContainer.innerHTML = "";

    // Tambahkan HTML untuk filter dengan icon dan dark mode support
    filterContainer.innerHTML = `
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-calendar-alt me-2 text-muted"></i>
            <div class="input-group input-group-sm">
                <input type="text" id="monthPicker" class="form-control" placeholder="Pilih Bulan" readonly data-bs-theme="auto">
                <input type="hidden" id="filter-bulan">
                <input type="hidden" id="filter-tahun">
                <button class="btn btn-outline-secondary" type="button" id="resetFilter" style="display: none;" data-bs-theme="auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

    // Pastikan Air Datepicker tersedia sebelum diinisialisasi
    if (typeof AirDatepicker === "undefined") {
        console.error(
            "❌ Air Datepicker tidak ditemukan! Pastikan CDN telah dimuat."
        );
        return;
    }

    // Check current theme
    const currentTheme = document.documentElement.getAttribute("data-bs-theme");
    const isDarkMode = currentTheme === "dark";

    // Inisialisasi Air Datepicker dengan dark mode support
    const datePicker = new AirDatepicker("#monthPicker", {
        view: "months",
        minView: "months",
        dateFormat: "MM/yyyy",
        autoClose: true,
        classes: isDarkMode ? "air-datepicker-dark" : "",
        onShow() {
            // Apply dark mode when picker shows
            setTimeout(() => {
                const pickerElement = document.querySelector(".air-datepicker");
                const currentTheme =
                    document.documentElement.getAttribute("data-bs-theme");

                if (pickerElement && currentTheme === "dark") {
                    // Apply inline styles directly
                    pickerElement.style.backgroundColor = "#212529";
                    pickerElement.style.border = "1px solid #495057";
                    pickerElement.style.color = "#f8f9fa";

                    // Apply to month cells only
                    const monthCells = pickerElement.querySelectorAll(
                        ".air-datepicker-cell.-month-"
                    );

                    monthCells.forEach((cell) => {
                        cell.style.color = "#f8f9fa";
                        cell.style.backgroundColor = "transparent";

                        // Add hover listeners
                        cell.addEventListener("mouseenter", function () {
                            if (
                                !this.classList.contains("-selected-") &&
                                !this.classList.contains("-current-")
                            ) {
                                this.style.backgroundColor = "#0d6efd";
                                this.style.color = "#ffffff";
                            }
                        });

                        cell.addEventListener("mouseleave", function () {
                            if (this.classList.contains("-selected-")) {
                                // Selected month - green background
                                this.style.backgroundColor = "#198754";
                                this.style.color = "#ffffff";
                            } else if (this.classList.contains("-current-")) {
                                // Current month - orange background
                                this.style.backgroundColor = "#fd7e14";
                                this.style.color = "#ffffff";
                            } else {
                                // Normal state
                                this.style.backgroundColor = "transparent";
                                this.style.color = "#f8f9fa";
                            }
                        });

                        // Apply initial states
                        if (cell.classList.contains("-selected-")) {
                            cell.style.backgroundColor = "#198754";
                            cell.style.color = "#ffffff";
                        } else if (cell.classList.contains("-current-")) {
                            cell.style.backgroundColor = "#fd7e14";
                            cell.style.color = "#ffffff";
                        }
                    });

                    // Apply to nav
                    const nav = pickerElement.querySelector(
                        ".air-datepicker-nav"
                    );
                    if (nav) {
                        nav.style.backgroundColor = "#212529";
                        nav.style.borderBottom = "1px solid #495057";

                        const navItems = nav.querySelectorAll(
                            ".air-datepicker-nav--title, .air-datepicker-nav--action"
                        );
                        navItems.forEach((item) => {
                            item.style.color = "#f8f9fa";
                        });
                    }
                }
            }, 10);
        },
        locale: {
            days: [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu",
            ],
            daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            daysMin: ["Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
            months: [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ],
            monthsShort: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Agu",
                "Sep",
                "Okt",
                "Nov",
                "Des",
            ],
            today: "Hari ini",
            clear: "Hapus",
            dateFormat: "MM/yyyy",
            firstDay: 1, // Senin sebagai hari pertama
        },
        onSelect({ date, formattedDate }) {
            if (!date) {
                console.warn("❌ Tanggal tidak valid!");
                return;
            }

            let bulan = String(date.getMonth() + 1).padStart(2, "0"); // Bulan dalam format MM
            let tahun = date.getFullYear(); // Tahun dalam format YYYY

            document.getElementById("filter-bulan").value = bulan;
            document.getElementById("filter-tahun").value = tahun;
            document.getElementById("monthPicker").value = formattedDate;

            document.getElementById("resetFilter").style.display =
                "inline-block";

            if (typeof onFilterChange === "function") {
                onFilterChange(bulan, tahun);
            }
        },
    });

    // Fungsi Reset Filter
    document
        .getElementById("resetFilter")
        .addEventListener("click", function (e) {
            e.preventDefault();
            // console.log("🔄 Reset filter diklik");

            document.getElementById("monthPicker").value = "";
            document.getElementById("filter-bulan").value = "";
            document.getElementById("filter-tahun").value = "";
            this.style.display = "none";

            // Setel Air Datepicker ke nilai kosong tanpa memicu event
            datePicker.clear();

            // Pastikan `onFilterChange` dipanggil hanya jika diperlukan
            if (typeof onFilterChange === "function") {
                onFilterChange("", "");
            }
        });

    // Listen untuk perubahan theme dan update datepicker
    document.addEventListener("themeChanged", function (event) {
        const isDark = event.detail.actualTheme === "dark";
        const pickerElement = document.querySelector(".air-datepicker");

        if (pickerElement) {
            if (isDark) {
                pickerElement.classList.add("air-datepicker-dark");
            } else {
                pickerElement.classList.remove("air-datepicker-dark");
            }
        }
    });

    // Store datepicker instance for theme changes
    window.currentDatePicker = datePicker;

    // console.log("✅ filterData.js selesai diinisialisasi");
}
