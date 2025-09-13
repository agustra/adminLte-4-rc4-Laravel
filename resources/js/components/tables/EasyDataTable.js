/**
 * EasyDataTable - Simple DataTable like jQuery DataTables
 * Usage: $('#table').easyDataTable({ apiUrl: '/api/data', columns: [...] })
 */

class EasyDataTable {
    constructor(selector, options = {}) {
        this.element =
            typeof selector === "string"
                ? document.querySelector(selector)
                : selector;
        if (!this.element) {
            throw new Error(`EasyDataTable: Element '${selector}' not found`);
        }

        this.options = {
            apiUrl: "",
            columns: [],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            ordering: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            responsive: true,
            select: false,
            buttons: [],
            autoLoad: true, // Auto load data setelah init
            onDataLoaded: null, // Callback setelah data dimuat
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "«",
                    last: "»",
                    next: "›",
                    previous: "‹",
                },
                processing: "Loading",
                emptyTable: "Tidak ada data tersedia",
            },
            ...options,
        };

        this.state = {
            currentPage: 1,
            pageSize: this.options.pageLength,
            total: 0,
            search: "",
            order: this.options.order || { column: 0, dir: "asc" },
            selectedRows: new Set(),
            filters: {},
            loading: false, // Flag untuk mencegah multiple request
        };

        this.init();
    }

    init() {
        this.initTouchEvents();
        this.createStructure();
        this.attachEvents();
        this.updateSortIcons(); // Update sort icons for default order

        // Auto load data jika diaktifkan
        if (this.options.autoLoad && this.options.apiUrl) {
            this.loadData();

            // Initialize responsive system setelah data dimuat
            if (this.options.responsive) {
                setTimeout(() => this.initResponsiveSystem(), 100);
            }
        }
    }

    createStructure() {
        // Create wrapper
        const wrapper = document.createElement("div");
        wrapper.className = "easy-datatable-wrapper";
        this.element.parentNode.insertBefore(wrapper, this.element);
        wrapper.appendChild(this.element);

        // Create top controls with 3 sections: Length | Buttons | Search
        const topControls = document.createElement("div");
        topControls.className =
            "easy-dt-top d-flex justify-content-between align-items-center mb-3";

        // Left: Length menu
        const leftSection = document.createElement("div");
        if (this.options.paging) {
            leftSection.innerHTML = `
                <label class="d-flex align-items-center gap-2">
                    ${this.options.language.lengthMenu.replace(
                        "_MENU_",
                        `<select class="form-select form-select-sm" style="width: auto;">
                            ${this.options.lengthMenu
                                .map(
                                    (len) =>
                                        `<option value="${len}" ${
                                            len === this.options.pageLength
                                                ? "selected"
                                                : ""
                                        }>${len}</option>`
                                )
                                .join("")}
                        </select>`
                    )}
                </label>
            `;
        }
        topControls.appendChild(leftSection);

        // Center: Buttons
        const centerSection = document.createElement("div");
        centerSection.className = "easy-dt-buttons";
        if (this.options.buttons && this.options.buttons.length > 0) {
            this.createButtons(centerSection);
        }
        topControls.appendChild(centerSection);

        // Right: Search box
        const rightSection = document.createElement("div");
        if (this.options.searching) {
            rightSection.innerHTML = `
                <label class="d-flex align-items-center gap-2">
                    ${this.options.language.search}
                    <input type="search" class="form-control form-control-sm" placeholder="Cari data...">
                </label>
            `;
        }
        topControls.appendChild(rightSection);

        wrapper.insertBefore(topControls, this.element);

        // Create table structure (keep existing classes from HTML)
        this.createTableHeaders();

        // Create tbody if not exists
        if (!this.element.querySelector("tbody")) {
            this.element.appendChild(document.createElement("tbody"));
        }

        // Create bottom controls
        const bottomControls = document.createElement("div");
        bottomControls.className =
            "easy-dt-bottom d-flex justify-content-between align-items-center mt-3";

        // Info
        if (this.options.info) {
            const infoDiv = document.createElement("div");
            infoDiv.className = "easy-dt-info";
            bottomControls.appendChild(infoDiv);
        }

        // Pagination
        if (this.options.paging) {
            const paginationDiv = document.createElement("div");
            paginationDiv.innerHTML =
                '<nav><ul class="pagination pagination-sm mb-0"></ul></nav>';
            bottomControls.appendChild(paginationDiv);
        }

        wrapper.appendChild(bottomControls);

        // Processing indicator
        if (this.options.processing) {
            const processing = document.createElement("div");
            processing.className =
                "easy-dt-processing position-absolute top-50 start-50 translate-middle d-none";
            processing.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-spinner fa-spin mb-2"></i>
                    <span class="fw-semibold fs-5">${this.options.language.processing}</span>
                </div>
            `;
            wrapper.style.position = "relative";
            wrapper.appendChild(processing);
        }

        // Add responsive CSS
        if (this.options.responsive) {
            this.addResponsiveCSS();
        }
    }

    addResponsiveCSS() {
        const style = document.createElement("style");
        style.textContent = `
/* Responsive Table Wrapper */
.easy-datatable-wrapper {
    width: 100%;
    overflow-x: auto;
}

@media (max-width: 768px) {
    .easy-datatable-wrapper {
        margin: 0 auto;
        text-align: center;
    }
    
    .easy-datatable-wrapper .table {
        margin: 0 auto;
    }
}

/* Compact Table Styling */
.easy-datatable-wrapper .table {
    margin-bottom: 0;
    table-layout: auto;
    width: 100%;
    min-width: 100%;
}

.easy-datatable-wrapper .table th,
.easy-datatable-wrapper .table td {
    padding: 6px 8px;
    font-size: 14px;
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.easy-datatable-wrapper .table th:first-child,
.easy-datatable-wrapper .table td:first-child {
    width: 30px;
    padding: 6px 4px;
    text-align: center;
}

/* Styling untuk kolom nomor DT_RowIndex */
.easy-datatable-wrapper .table th:nth-child(2),
.easy-datatable-wrapper .table td:nth-child(2) {
    width: 50px;
    text-align: center;
    font-weight: 500;
}

/* Responsive Controls */

.dtr-control {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border: 1px solid #007bff;
    border-radius: 2px;
    background: #fff;
    color: #007bff;
    cursor: pointer;
    font-size: 10px;
    font-weight: bold;
    transition: all 0.2s ease;
}

.dtr-control:before {
    content: '+';
}

.dtr-control.open {
    background: #007bff;
    color: #fff;
}

.dtr-control.open:before {
    content: '−';
}

.dtr-control:hover {
    background: #0056b3;
    color: #fff;
    border-color: #0056b3;
}

/* Detail Row Styling */
.detail-row {
    background: #f8f9fa !important;
}

.detail-row td {
    padding: 0 !important;
    border: none !important;
}

.detail-content {
    padding: 12px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin: 3px;
}

.detail-item {
    display: flex;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #eee;
}

.detail-item:last-child {
    margin-bottom: 0;
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #495057;
    min-width: 100px;
    margin-right: 8px;
    font-size: 12px;
}

.detail-value {
    flex: 1;
    color: #212529;
    font-size: 12px;
    word-break: break-word;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .easy-dt-top {
        flex-direction: column;
        gap: 8px;
        align-items: stretch !important;
    }
    
    .easy-dt-top > div {
        width: 100%;
    }
    
    .easy-dt-buttons {
        order: -1;
        text-align: center;
        margin: 0 auto;
        justify-content: center;
        display: flex;
    }
    
    .easy-dt-bottom {
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
    
    .detail-item {
        flex-direction: column;
        gap: 2px;
    }
    
    .detail-label {
        min-width: auto;
        font-weight: 700;
        color: #6c757d;
        font-size: 10px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    
    .detail-value {
        font-size: 13px;
    }
    
    /* Hide button text on mobile, show only icons */
    .easy-dt-buttons .btn {
        padding: 4px 8px;
        font-size: 12px;
        min-width: 32px;
        text-align: center;
    }
    
    .easy-dt-buttons .btn .btn-text {
        display: none;
    }
    
    .easy-dt-buttons .btn i {
        margin: 0;
    }
    
    /* Responsive pagination */
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
        gap: 2px;
    }
    
    .pagination .page-item .page-link {
        padding: 4px 8px;
        font-size: 12px;
        min-width: 32px;
        text-align: center;
    }
    
    .pagination .page-item:nth-child(n+6):nth-last-child(n+6) {
        display: none;
    }
}

/* Hide control in header always */
.table thead .dtr-control {
    display: none !important;
}

/* Animation */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.detail-row {
    animation: slideDown 0.2s ease;
}


    /* =========================== */
    /* ===== UNTUK PROCESSING /  LOADING =====*/
    /* =========================== */
    .easy-dt-processing {
        /* display: none; */
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: none;
        background-color: transparent;
        padding: 0;
        width: 100px;
        height: 100px;
        text-align: center;
        line-height: 100px;
        /* background-image: url('{{ asset('img/loading-gif.gif') }}'); */
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .easy-dt-processing i.fa-spinner {
        font-size: 5rem;
        margin-top: 0;
        animation: spin 2s linear infinite;
    }

    /* add animation keyframes */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* add color gradient effect */
    .easy-dt-processing i.fa-spinner {
        color: #FFC107;
        /* updated color */
        background: linear-gradient(to bottom, #FFC107, #FF69B4);
        /* background: linear-gradient(to bottom, #3498db, #f1c40f, #632f53, #d11e48, #f4dd51, #a1c5ab, #fde6bd); */
        /* add gradient effect */
        background-clip: text;
        /* clip the gradient to the text */
        -webkit-background-clip: text;
        /* for webkit browsers */
        -webkit-text-fill-color: transparent;
        /* for webkit browsers */
    }
    /* =========================== */
    /* ===== UNTUK PROCESING /  LOADING =====*/
    /* =========================== */

    /* =========================== */
    /* ===== START PAGINATION =====*/
    /* =========================== */

    .pagination>li>a,
    .pagination>li>span {
        /* Properti lainnya tetap sama */
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        border-radius: 50% !important;
        height: 30px !important;
        width: 30px !important;
        color: #4285f4 !important;
        margin: 0 2px !important;
        padding: 5px 0 !important;
        /* Menambahkan padding atas dan bawah untuk menaikkan teks */
        border: 1px solid #e0e0e0 !important;
        text-decoration: none !important;
        font-size: 12px !important;
    }

    .pagination>li>a>span {
        line-height: 1px;
        padding-bottom: 3px;
    }

    .pagination>li.active>a {
        background-color: #4285f4 !important;
        color: white !important;
    }

    .pagination>li>a:hover {
        background-color: #e0e0e0 !important;
    }

    .rows-per-page {
        max-width: 65px;
        /* Atur lebar maksimum */
        min-width: 65px;
        /* Atur lebar minimum agar tidak terlalu kecil */
    }

    /* =========================== */
    /* ===== END PAGINATION =====*/
    /* =========================== */


        `;
        document.head.appendChild(style);
    }

    createTableHeaders() {
        let thead = this.element.querySelector("thead");
        if (!thead) {
            thead = document.createElement("thead");
            this.element.appendChild(thead);
        }

        const tr = document.createElement("tr");

        // Add responsive control + select column if enabled
        if (this.options.responsive || this.options.select) {
            const th = document.createElement("th");
            let content = "";

            if (this.options.responsive) {
                // Empty header for responsive control column
                content += "";
            }

            if (this.options.select) {
                content +=
                    '<input type="checkbox" class="form-check-input select-all">';
            }

            th.innerHTML = content;
            th.style.width = this.options.select ? "50px" : "30px";
            tr.appendChild(th);
        }

        // Add columns
        this.options.columns.forEach((column, index) => {
            const th = document.createElement("th");
            th.textContent = column.title || column.data;

            // Add column className if provided
            if (column.className) {
                th.className = column.className;
            }

            if (this.options.ordering && column.orderable !== false) {
                th.classList.add("sortable");
                th.style.cursor = "pointer";
                th.setAttribute("data-column", index);
                th.innerHTML = `${
                    column.title || column.data
                } <i class="fas fa-sort ms-1 sort-icon"></i>`;
            }

            tr.appendChild(th);
        });

        thead.innerHTML = "";
        thead.appendChild(tr);
    }

    attachEvents() {
        const wrapper = this.element.closest(".easy-datatable-wrapper");

        // Length change
        const lengthSelect = wrapper.querySelector("select");
        if (lengthSelect) {
            lengthSelect.addEventListener("change", (e) => {
                this.state.pageSize = parseInt(e.target.value);
                this.state.currentPage = 1;
                this.loadData();
            });
        }

        // Search
        const searchInput = wrapper.querySelector('input[type="search"]');
        if (searchInput) {
            let timeout;
            searchInput.addEventListener("input", (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.state.search = e.target.value;
                    this.state.currentPage = 1;
                    this.loadData();
                }, 300);
            });
        }

        // Column sorting
        if (this.options.ordering) {
            this.element.addEventListener("click", (e) => {
                const th = e.target.closest("th.sortable");
                if (th) {
                    const columnIndex = parseInt(
                        th.getAttribute("data-column")
                    );
                    const currentDir =
                        this.state.order.column === columnIndex
                            ? this.state.order.dir
                            : "asc";
                    const newDir = currentDir === "asc" ? "desc" : "asc";

                    this.state.order = { column: columnIndex, dir: newDir };
                    this.updateSortIcons();
                    this.loadData();
                }
            });
        }

        // Select all - Event delegation untuk checkbox yang dibuat dinamis
        if (this.options.select) {
            // Event delegation untuk select all checkbox
            wrapper.addEventListener("change", (e) => {
                if (e.target.classList.contains("select-all")) {
                    const checkboxes = this.element.querySelectorAll(
                        'tbody input[type="checkbox"].row-select'
                    );
                    checkboxes.forEach((cb) => {
                        cb.checked = e.target.checked;
                        const id = cb.getAttribute("data-id");
                        if (e.target.checked) {
                            this.state.selectedRows.add(id);
                        } else {
                            this.state.selectedRows.delete(id);
                        }
                    });
                    this.triggerSelectEvent();
                }
            });
        }
    }

    async loadData() {
        if (!this.options.apiUrl) return;

        // Prevent multiple simultaneous requests
        if (this.state.loading) {
            // console.log('EasyDataTable: Request already in progress, skipping...');
            return;
        }

        this.state.loading = true;
        this.showProcessing(true);

        try {
            // Import axiosClient dynamically
            const { default: axiosClient } = await import(
                "@api/axiosClient.js"
            );

            const params = this.buildParams();
            const response = await axiosClient.get(this.options.apiUrl, {
                params,
            });
            const responseData = response.data;

            // Handle multiple data response (array) atau single response (object)
            let tableData, additionalData;

            if (Array.isArray(responseData)) {
                // Multiple data: [tableData, additionalData, ...]
                tableData = responseData[0] || {};
                additionalData = responseData.slice(1); // Semua data tambahan
            } else {
                // Single data response
                tableData = responseData;
                additionalData = [];
            }

            this.renderData(tableData.data || []);
            this.updateInfo(tableData.meta || {});
            this.updatePagination(tableData.meta || {});

            // Trigger onDataLoaded callback dengan semua data
            if (
                this.options.onDataLoaded &&
                typeof this.options.onDataLoaded === "function"
            ) {
                this.options.onDataLoaded({
                    table: tableData,
                    additional: additionalData,
                    raw: responseData, // Raw response untuk akses penuh
                });
            }
        } catch (error) {
            console.error("EasyDataTable: Error loading data", error);

            // Show toast notification for API errors
            if (error.response && error.response.data) {
                const errorData = error.response.data;
                const message = errorData.message || "An error occurred";
                const status = error.response.status;

                // Show toast notification
                this.showNotification(`Error ${status}: ${message}`, "error");
            } else {
                this.showNotification("Network error occurred", "error");
            }

            this.renderError();
        } finally {
            this.state.loading = false;
            this.showProcessing(false);
        }
    }

    buildParams() {
        const params = {
            page: this.state.currentPage,
            size: this.state.pageSize,
            search: this.state.search,
        };

        // Tambahkan filters jika ada
        if (this.state.filters) {
            Object.assign(params, this.state.filters);
        }

        if (this.options.ordering && this.state.order) {
            const column = this.options.columns[this.state.order.column];
            if (column) {
                params.sort_column = column.data;
                params.sort_dir = this.state.order.dir;
            }
        }

        // Execute data callback function jika ada
        if (this.options.data && typeof this.options.data === "function") {
            this.options.data(params);
        }

        return params;
    }

    renderData(data) {
        const tbody = this.element.querySelector("tbody");
        tbody.innerHTML = "";

        if (!data.length) {
            const tr = document.createElement("tr");
            const td = document.createElement("td");
            td.colSpan =
                this.options.columns.length + (this.options.select ? 1 : 0);
            td.textContent = this.options.language.emptyTable;
            td.className = "text-center";
            tr.appendChild(td);
            tbody.appendChild(tr);
            return;
        }

        data.forEach((row, index) => {
            const tr = document.createElement("tr");

            // Responsive control + Select checkbox
            if (this.options.responsive || this.options.select) {
                const td = document.createElement("td");
                let content = "";

                if (this.options.responsive) {
                    content += `<span class="dtr-control me-1 rounded" data-id="${
                        row.id || index
                    }" style="cursor: pointer;"></span>`;
                }

                if (this.options.select) {
                    content += `<input type="checkbox" class="form-check-input row-select" data-id="${
                        row.id || index
                    }">`;
                }

                td.innerHTML = content;
                tr.appendChild(td);
            }

            // Data columns
            this.options.columns.forEach((column) => {
                const td = document.createElement("td");

                // Add column className if provided
                if (column.className) {
                    td.className = column.className;
                }

                // Handle DT_RowIndex for auto numbering
                if (column.data === "DT_RowIndex") {
                    const rowNumber =
                        (this.state.currentPage - 1) * this.state.pageSize +
                        index +
                        1;
                    td.textContent = rowNumber;
                    td.className = "text-center";
                } else if (
                    column.render &&
                    typeof column.render === "function"
                ) {
                    td.innerHTML = column.render(
                        row[column.data],
                        "display",
                        row
                    );
                } else {
                    td.textContent = row[column.data] || "";
                }

                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        // Attach row select events dengan update select-all checkbox
        if (this.options.select) {
            tbody.querySelectorAll(".row-select").forEach((cb) => {
                cb.addEventListener("change", (e) => {
                    const id = e.target.getAttribute("data-id");
                    if (e.target.checked) {
                        this.state.selectedRows.add(id);
                    } else {
                        this.state.selectedRows.delete(id);
                    }

                    // Update select-all checkbox state
                    this.updateSelectAllCheckbox();
                    this.triggerSelectEvent();
                });
            });
        }

        // Event handler untuk responsive detail row
        if (this.options.responsive) {
            tbody.querySelectorAll(".dtr-control").forEach((control, index) => {
                control.addEventListener("click", (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const currentRow = control.closest("tr");
                    const existingDetail = currentRow.nextElementSibling;
                    const rowData = data[index];

                    // Tutup semua detail row lainnya
                    document.querySelectorAll(".detail-row").forEach((row) => {
                        if (row !== existingDetail) {
                            row.remove();
                        }
                    });

                    document
                        .querySelectorAll(".dtr-control.open")
                        .forEach((ctrl) => {
                            if (ctrl !== control) {
                                ctrl.classList.remove("open");
                            }
                        });

                    // Toggle detail row
                    if (
                        existingDetail &&
                        existingDetail.classList.contains("detail-row")
                    ) {
                        existingDetail.remove();
                        control.classList.remove("open");
                    } else {
                        this.createDetailRow(currentRow, rowData);
                        control.classList.add("open");
                    }
                });
            });
        }

        // Update responsive columns setelah render data
        if (this.options.responsive) {
            setTimeout(() => this.updateResponsiveColumns(), 10);
        }

        // Update select-all checkbox state setelah render data
        if (this.options.select) {
            setTimeout(() => this.updateSelectAllCheckbox(), 10);
        }
    }

    updateInfo(meta) {
        const infoDiv = this.element
            .closest(".easy-datatable-wrapper")
            .querySelector(".easy-dt-info");
        if (!infoDiv) return;

        const total = meta.total || 0;
        const start =
            total > 0
                ? (this.state.currentPage - 1) * this.state.pageSize + 1
                : 0;
        const end = Math.min(
            this.state.currentPage * this.state.pageSize,
            total
        );

        let info = this.options.language.info
            .replace("_START_", start)
            .replace("_END_", end)
            .replace("_TOTAL_", total);

        if (this.state.search) {
            info +=
                " " +
                this.options.language.infoFiltered.replace("_MAX_", total);
        }

        infoDiv.textContent = info;
    }

    updatePagination(meta) {
        const pagination = this.element
            .closest(".easy-datatable-wrapper")
            .querySelector(".pagination");
        if (!pagination) return;

        const total = meta.total || 0;
        const totalPages = Math.ceil(total / this.state.pageSize);

        pagination.innerHTML = "";

        if (totalPages <= 1) return;

        // First
        const firstLi = document.createElement("li");
        firstLi.className = `page-item ${
            this.state.currentPage === 1 ? "disabled" : ""
        }`;
        firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">${this.options.language.paginate.first}</a>`;
        pagination.appendChild(firstLi);

        // Previous
        const prevLi = document.createElement("li");
        prevLi.className = `page-item ${
            this.state.currentPage === 1 ? "disabled" : ""
        }`;
        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${
            this.state.currentPage - 1
        }">${this.options.language.paginate.previous}</a>`;
        pagination.appendChild(prevLi);

        // Pages with ellipsis - responsive based on screen size
        const isMobile = window.innerWidth <= 768;
        const maxButtons = isMobile ? 3 : 7; // Less buttons on mobile
        let startPage, endPage;

        if (totalPages <= maxButtons) {
            // Show all pages if total is small
            startPage = 1;
            endPage = totalPages;
        } else {
            // Calculate start and end with current page in center
            const maxPagesBeforeCurrentPage = Math.floor(maxButtons / 2);
            const maxPagesAfterCurrentPage = Math.ceil(maxButtons / 2) - 1;

            if (this.state.currentPage <= maxPagesBeforeCurrentPage) {
                startPage = 1;
                endPage = maxButtons;
            } else if (
                this.state.currentPage + maxPagesAfterCurrentPage >=
                totalPages
            ) {
                startPage = totalPages - maxButtons + 1;
                endPage = totalPages;
            } else {
                startPage = this.state.currentPage - maxPagesBeforeCurrentPage;
                endPage = this.state.currentPage + maxPagesAfterCurrentPage;
            }
        }

        // Add first page and ellipsis if needed (skip on mobile if too many pages)
        if (!isMobile && startPage > 2) {
            const firstLi = document.createElement("li");
            firstLi.className = "page-item";
            firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
            pagination.appendChild(firstLi);

            if (startPage > 3) {
                const ellipsisLi = document.createElement("li");
                ellipsisLi.className = "page-item disabled";
                ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                pagination.appendChild(ellipsisLi);
            }
        } else if (!isMobile && startPage === 2) {
            const firstLi = document.createElement("li");
            firstLi.className = "page-item";
            firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
            pagination.appendChild(firstLi);
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            const li = document.createElement("li");
            li.className = `page-item ${
                i === this.state.currentPage ? "active" : ""
            }`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            pagination.appendChild(li);
        }

        // Add last page and ellipsis if needed (skip on mobile if too many pages)
        if (!isMobile && endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsisLi = document.createElement("li");
                ellipsisLi.className = "page-item disabled";
                ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                pagination.appendChild(ellipsisLi);
            }

            const lastLi = document.createElement("li");
            lastLi.className = "page-item";
            lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
            pagination.appendChild(lastLi);
        }

        // Next
        const nextLi = document.createElement("li");
        nextLi.className = `page-item ${
            this.state.currentPage === totalPages ? "disabled" : ""
        }`;
        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${
            this.state.currentPage + 1
        }">${this.options.language.paginate.next}</a>`;
        pagination.appendChild(nextLi);

        // Last
        const lastLi = document.createElement("li");
        lastLi.className = `page-item ${
            this.state.currentPage === totalPages ? "disabled" : ""
        }`;
        lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${this.options.language.paginate.last}</a>`;
        pagination.appendChild(lastLi);

        // Remove existing event listeners to prevent multiple requests
        const existingPagination = this.element
            .closest(".easy-datatable-wrapper")
            .querySelector(".pagination");
        if (existingPagination && existingPagination._paginationHandler) {
            existingPagination.removeEventListener(
                "click",
                existingPagination._paginationHandler
            );
        }

        // Create new event handler
        const paginationHandler = (e) => {
            e.preventDefault();
            const link = e.target.closest("a[data-page]");
            if (link && !link.closest(".disabled")) {
                this.state.currentPage = parseInt(
                    link.getAttribute("data-page")
                );
                this.loadData();
            }
        };

        // Attach new event listener
        pagination.addEventListener("click", paginationHandler);
        pagination._paginationHandler = paginationHandler;
    }

    updateSortIcons() {
        const headers = this.element.querySelectorAll("th.sortable");
        headers.forEach((th, index) => {
            const icon = th.querySelector("i");
            if (icon) {
                icon.className =
                    "fas ms-1 " +
                    (index === this.state.order.column
                        ? this.state.order.dir === "asc"
                            ? "fa-sort-up"
                            : "fa-sort-down"
                        : "fa-sort");
            }
        });
    }

    showProcessing(show) {
        const processing = this.element
            .closest(".easy-datatable-wrapper")
            .querySelector(".easy-dt-processing");
        if (processing) {
            processing.classList.toggle("d-none", !show);
        }
    }

    renderError() {
        const tbody = this.element.querySelector("tbody");
        tbody.innerHTML = `
            <tr>
                <td colspan="${
                    this.options.columns.length + (this.options.select ? 1 : 0)
                }" class="text-center text-danger">
                    Error loading data
                </td>
            </tr>
        `;
    }

    triggerSelectEvent() {
        const event = new CustomEvent("select", {
            detail: {
                selectedRows: Array.from(this.state.selectedRows),
            },
        });
        this.element.dispatchEvent(event);
    }

    // Public methods
    reload(filters = {}) {
        // Simpan filters ke state
        this.state.filters = filters;
        this.state.currentPage = 1; // Reset ke halaman pertama
        this.loadData();
    }

    search(value) {
        this.state.search = value;
        this.state.currentPage = 1;
        this.loadData();
    }

    page(pageNumber) {
        this.state.currentPage = pageNumber;
        this.loadData();
    }

    getSelectedRows() {
        return Array.from(this.state.selectedRows);
    }

    clearSelection() {
        this.state.selectedRows.clear();
        const checkboxes = this.element.querySelectorAll(
            'input[type="checkbox"]'
        );
        checkboxes.forEach((cb) => (cb.checked = false));
        this.updateSelectAllCheckbox();
        this.triggerSelectEvent();
    }

    // Method untuk update status select-all checkbox
    updateSelectAllCheckbox() {
        const wrapper = this.element.closest(".easy-datatable-wrapper");
        const selectAllCheckbox = wrapper.querySelector(".select-all");
        const rowCheckboxes =
            this.element.querySelectorAll("tbody .row-select");

        if (selectAllCheckbox && rowCheckboxes.length > 0) {
            const checkedCount = Array.from(rowCheckboxes).filter(
                (cb) => cb.checked
            ).length;

            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === rowCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    updateConfig(newOptions) {
        this.options = { ...this.options, ...newOptions };
        this.createTableHeaders();
        this.loadData();
    }

    // Create buttons
    createButtons(container) {
        const buttonContainer = document.createElement("div");
        buttonContainer.className = "d-flex gap-1";

        this.options.buttons.forEach((buttonConfig) => {
            if (typeof buttonConfig === "string") {
                // Built-in buttons
                const button = this.createBuiltInButton(buttonConfig);
                if (button) buttonContainer.appendChild(button);
            } else if (typeof buttonConfig === "object") {
                // Custom buttons
                const button = this.createCustomButton(buttonConfig);
                if (button) buttonContainer.appendChild(button);
            }
        });

        container.appendChild(buttonContainer);
    }

    // Create built-in buttons
    createBuiltInButton(type) {
        const button = document.createElement("button");

        const configs = {
            copy: {
                text: "Copy",
                icon: "fas fa-copy",
                className: "btn-info",
                action: () => this.exportCopy(),
            },
            csv: {
                text: "CSV",
                icon: "fas fa-file-csv",
                className: "btn-success",
                action: () => this.exportCSV(),
            },
            excel: {
                text: "Excel",
                icon: "fas fa-file-excel",
                className: "btn-warning",
                action: () => this.exportExcel(),
            },
            pdf: {
                text: "PDF",
                icon: "fas fa-file-pdf",
                className: "btn-danger",
                action: () => this.exportPDF(),
            },
            print: {
                text: "Print",
                icon: "fas fa-print",
                className: "btn-secondary",
                action: () => this.exportPrint(),
            },
            colvis: {
                text: "Columns",
                icon: "fas fa-columns",
                className: "btn-info",
                action: () => this.toggleColumnVisibility(),
            },
        };

        const config = configs[type];
        if (!config) return null;

        // Set button properties
        button.type = "button";
        button.className = `btn ${config.className} btn-sm`;
        button.innerHTML = `<i class="${config.icon}"></i> <span class="btn-text">${config.text}</span>`;
        button.addEventListener("click", config.action);

        return button;
    }

    // Create custom buttons
    createCustomButton(config) {
        const button = document.createElement("button");
        button.type = "button";
        button.className =
            config.className || "btn btn-outline-secondary btn-sm";
        button.innerHTML = config.text || "Button";

        // Set ID if provided
        if (config.id) {
            button.id = config.id;
        }

        // Set style if provided
        if (config.style) {
            button.setAttribute("style", config.style);
        }

        if (config.action && typeof config.action === "function") {
            button.addEventListener("click", () => config.action(this));
        }

        return button;
    }

    // Export functions
    exportCopy() {
        const data = this.getCurrentTableData();
        const text = this.formatDataForCopy(data);

        navigator.clipboard
            .writeText(text)
            .then(() => {
                this.showNotification("Data copied to clipboard", "success");
            })
            .catch(() => {
                this.showNotification("Failed to copy data", "error");
            });
    }

    exportCSV() {
        const data = this.getCurrentTableData();
        const csv = this.formatDataForCSV(data);
        this.downloadFile(csv, "text/csv", "export.csv");
        this.showNotification("CSV exported successfully", "success");
    }

    exportExcel() {
        const data = this.getCurrentTableData();
        const xml = this.formatDataForExcel(data);
        this.downloadFile(xml, "application/vnd.ms-excel", "export.xls");
        this.showNotification("Excel exported successfully", "success");
    }

    exportPDF() {
        const data = this.getCurrentTableData();
        this.generatePDF(data);
    }

    exportPrint() {
        const data = this.getCurrentTableData();
        const printContent = this.formatDataForPrint(data);

        const printWindow = window.open("", "_blank", "width=800,height=600");
        if (printWindow) {
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.onload = () => {
                printWindow.focus();
                printWindow.print();
                setTimeout(() => printWindow.close(), 1000);
            };
        }
    }

    // Get current table data
    getCurrentTableData() {
        const headers = Array.from(
            this.element.querySelectorAll("thead th")
        ).map((th) => th.textContent.trim());
        const rows = Array.from(this.element.querySelectorAll("tbody tr")).map(
            (tr) =>
                Array.from(tr.querySelectorAll("td")).map((td) =>
                    td.textContent.trim()
                )
        );
        return { headers, rows };
    }

    // Format data for different exports
    formatDataForCopy(data) {
        const lines = [data.headers.join("\t")];
        data.rows.forEach((row) => lines.push(row.join("\t")));
        return lines.join("\n");
    }

    formatDataForCSV(data) {
        const lines = [data.headers.map((h) => `"${h}"`).join(",")];
        data.rows.forEach((row) => {
            lines.push(row.map((cell) => `"${cell}"`).join(","));
        });
        return lines.join("\n");
    }

    formatDataForExcel(data) {
        let xml =
            '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>';
        xml +=
            '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet">';
        xml += "<Worksheet><Table>";

        // Headers
        xml += "<Row>";
        data.headers.forEach((header) => {
            xml += `<Cell><Data ss:Type="String">${header}</Data></Cell>`;
        });
        xml += "</Row>";

        // Data rows
        data.rows.forEach((row) => {
            xml += "<Row>";
            row.forEach((cell) => {
                xml += `<Cell><Data ss:Type="String">${cell}</Data></Cell>`;
            });
            xml += "</Row>";
        });

        xml += "</Table></Worksheet></Workbook>";
        return xml;
    }

    formatDataForPrint(data) {
        return `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Data</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                </style>
            </head>
            <body>
                <h2>Data Export - ${new Date().toLocaleDateString()}</h2>
                <table>
                    <thead>
                        <tr>${data.headers
                            .map((h) => `<th>${h}</th>`)
                            .join("")}</tr>
                    </thead>
                    <tbody>
                        ${data.rows
                            .map(
                                (row) =>
                                    `<tr>${row
                                        .map((cell) => `<td>${cell}</td>`)
                                        .join("")}</tr>`
                            )
                            .join("")}
                    </tbody>
                </table>
            </body>
            </html>
        `;
    }

    generatePDF(data) {
        // Simple PDF generation fallback
        this.exportPrint();
        this.showNotification(
            'Use browser print dialog and select "Save as PDF"',
            "info"
        );
    }

    // Download file helper
    downloadFile(content, type, filename) {
        const blob = new Blob([content], { type });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Show notification
    showNotification(message, type = "info") {
        // Try different notification systems
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: type === "error" ? "Error" : "Success",
                text: message,
                icon: type === "error" ? "error" : "success",
                timer: 2000,
                showConfirmButton: false,
            });
        } else if (typeof toastr !== "undefined") {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    // Initialize responsive system
    initResponsiveSystem() {
        this.updateResponsiveColumns();

        // Update saat resize
        let resizeTimeout;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.updateResponsiveColumns();
            }, 150);
        });
    }

    // Update kolom responsive berdasarkan lebar container
    updateResponsiveColumns() {
        if (!this.options.responsive) return;

        const hiddenColumns = this.getHiddenColumns();
        const hasHiddenColumns = hiddenColumns.length > 0;

        // Update visibility kolom
        const headers = this.element.querySelectorAll("thead th");
        const rows = this.element.querySelectorAll("tbody tr:not(.detail-row)");

        headers.forEach((th, index) => {
            if (index === 0) return; // Skip kolom kontrol
            const columnIndex = index - 1;
            th.style.display = hiddenColumns.includes(columnIndex)
                ? "none"
                : "";
        });

        rows.forEach((row) => {
            const cells = row.querySelectorAll("td");
            cells.forEach((td, index) => {
                if (index === 0) return; // Skip kolom kontrol
                const columnIndex = index - 1;
                td.style.display = hiddenColumns.includes(columnIndex)
                    ? "none"
                    : "";
            });
        });

        // Update visibility tombol expand
        const dtrControls = this.element.querySelectorAll(".dtr-control");
        dtrControls.forEach((control) => {
            control.style.display = hasHiddenColumns ? "inline-flex" : "none";
        });

        // Set proportional column widths untuk memenuhi layar
        this.setProportionalWidths(hiddenColumns);
    }

    // Column visibility toggle
    toggleColumnVisibility() {
        this.showNotification(
            "Gunakan responsive breakpoints untuk mengatur kolom",
            "info"
        );
    }

    // Destroy method - Clean up everything
    destroy() {
        const wrapper = this.element.closest(".easy-datatable-wrapper");
        if (wrapper) {
            const parent = wrapper.parentNode;

            // Remove all event listeners
            if (wrapper._paginationHandler) {
                wrapper.removeEventListener(
                    "click",
                    wrapper._paginationHandler
                );
            }

            // Store original element attributes
            const originalId = this.element.id;
            const originalClass = this.element.className;
            const tagName = this.element.tagName.toLowerCase();

            // Remove wrapper completely
            wrapper.remove();

            // Restore original element with its attributes
            const restoredElement = document.createElement(tagName);
            if (originalId) restoredElement.id = originalId;
            if (originalClass) restoredElement.className = originalClass;

            parent.appendChild(restoredElement);
        }

        // Clear global references
        if (window.masterTable === this) {
            window.masterTable = null;
        }
    }

    createDetailRow(currentRow, rowData) {
        const detailRow = document.createElement("tr");
        detailRow.className = "detail-row";

        const detailCell = document.createElement("td");
        const columnCount = this.element.querySelectorAll("thead th").length;
        detailCell.colSpan = columnCount;

        // Dapatkan kolom yang tersembunyi
        const hiddenColumns = this.getHiddenColumns();

        let detailContent = '<div class="detail-content">';

        // Tampilkan semua kolom yang tersembunyi
        hiddenColumns.forEach((columnIndex) => {
            const column = this.options.columns[columnIndex];
            if (!column) return;

            let value = rowData[column.data] || "";

            // Gunakan render function jika ada
            if (column.render && typeof column.render === "function") {
                value = column.render(value, "display", rowData);
            }

            // Bersihkan HTML untuk tampilan text
            if (typeof value === "string" && value.includes("<")) {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = value;
                value = tempDiv.textContent || tempDiv.innerText || value;
            }

            detailContent += `
                <div class="detail-item">
                    <div class="detail-label">${
                        column.title || column.data
                    }</div>
                    <div class="detail-value">${value}</div>
                </div>
            `;
        });

        detailContent += "</div>";
        detailCell.innerHTML = detailContent;
        detailRow.appendChild(detailCell);

        // Sisipkan setelah current row
        currentRow.parentNode.insertBefore(detailRow, currentRow.nextSibling);
    }

    // Helper method untuk mendapatkan kolom yang tersembunyi berdasarkan lebar container
    getHiddenColumns() {
        const hiddenColumns = [];
        const containerWidth = this.element.closest(
            ".easy-datatable-wrapper"
        ).offsetWidth;

        // Estimasi lebar kolom berdasarkan konten
        const columnWidths = this.estimateColumnWidths();
        let totalWidth = 25; // Lebar kolom kontrol

        this.options.columns.forEach((column, index) => {
            const estimatedWidth = columnWidths[index] || 100;

            if (totalWidth + estimatedWidth < containerWidth * 0.98) {
                totalWidth += estimatedWidth;
            } else {
                hiddenColumns.push(index);
            }
        });

        return hiddenColumns;
    }

    // Estimasi lebar kolom berdasarkan konfigurasi dan konten
    estimateColumnWidths() {
        const widths = [];

        this.options.columns.forEach((column) => {
            // Gunakan width dari konfigurasi kolom jika ada
            if (column.width) {
                widths.push(parseInt(column.width));
                return;
            }

            // Estimasi otomatis berdasarkan title length
            const title = column.title || column.data || "";
            const minWidth = 60;
            const maxWidth = 200;
            const estimatedWidth = Math.max(
                minWidth,
                Math.min(maxWidth, title.length * 8 + 40)
            );

            widths.push(estimatedWidth);
        });

        return widths;
    }

    // Set lebar kolom berdasarkan estimasi atau konfigurasi
    setProportionalWidths(hiddenColumns) {
        const headers = this.element.querySelectorAll("thead th");
        const columnWidths = this.estimateColumnWidths();
        let totalEstimatedWidth = 30; // Kolom kontrol

        // Hitung total lebar kolom yang terlihat
        this.options.columns.forEach((column, index) => {
            if (!hiddenColumns.includes(index)) {
                totalEstimatedWidth += columnWidths[index];
            }
        });

        // Set width untuk setiap kolom
        headers.forEach((th, index) => {
            if (index === 0) {
                th.style.width = "30px"; // Kolom kontrol fixed
            } else {
                const columnIndex = index - 1;
                if (!hiddenColumns.includes(columnIndex)) {
                    const column = this.options.columns[columnIndex];
                    if (column && column.width) {
                        th.style.width = column.width;
                    } else {
                        // Auto width berdasarkan konten
                        th.style.width = "auto";
                    }
                }
            }
        });
    }
    initTouchEvents() {
        // Touch support untuk mobile devices
        if ("ontouchstart" in window) {
            let touchStarted = false;

            this.element.addEventListener(
                "touchstart",
                (e) => {
                    const control = e.target.closest(".dtr-control");
                    if (control) {
                        touchStarted = true;
                        control.style.transform = "scale(0.95)";
                    }
                },
                { passive: true }
            );

            this.element.addEventListener(
                "touchend",
                (e) => {
                    const control = e.target.closest(".dtr-control");
                    if (control && touchStarted) {
                        control.style.transform = "";
                        touchStarted = false;
                    }
                },
                { passive: true }
            );
        }
    }
}

// Tambahkan method ini dan panggil di init()

// jQuery-like plugin
if (typeof $ !== "undefined") {
    $.fn.easyDataTable = function (options) {
        return this.each(function () {
            if (!this.easyDataTable) {
                this.easyDataTable = new EasyDataTable(this, options);
            }
            return this.easyDataTable;
        });
    };
}

// Export for modules
if (typeof module !== "undefined" && module.exports) {
    module.exports = EasyDataTable;
}

// Global export
window.EasyDataTable = EasyDataTable;
