var E=(h,e)=>()=>(e||h((e={exports:{}}).exports,e),e.exports);import{M as v,i as C,a as k,I as S,b as D,C as T,o as L}from"./MediaLibrary-DzZMThYK.js";import{_ as M}from"./preload-helper-BfFHrpNk.js";import N from"./axiosClient-8LtfCK6A.js";import{D as P}from"./delete-QRht9yhg.js";import{s as b}from"./modalHandler-Bg5eHBCv.js";import{f as A}from"./fetchAxios-Cye8cR7v.js";import"./badgeUpdater-D1PKXa5-.js";var _=E((G,y)=>{let w=class{constructor(e,t={}){if(this.element=typeof e=="string"?document.querySelector(e):e,!this.element)throw new Error(`EasyDataTable: Element '${e}' not found`);this.options={apiUrl:"",columns:[],pageLength:10,lengthMenu:[10,25,50,100],searching:!0,ordering:!0,paging:!0,info:!0,processing:!0,serverSide:!0,responsive:!0,select:!1,buttons:[],autoLoad:!0,onDataLoaded:null,language:{search:"Cari:",lengthMenu:"Tampilkan _MENU_ data",info:"Menampilkan _START_ sampai _END_ dari _TOTAL_ data",infoEmpty:"Menampilkan 0 sampai 0 dari 0 data",infoFiltered:"(disaring dari _MAX_ total data)",paginate:{first:"«",last:"»",next:"›",previous:"‹"},processing:"Loading",emptyTable:"Tidak ada data tersedia"},...t},this.state={currentPage:1,pageSize:this.options.pageLength,total:0,search:"",order:this.options.order||{column:0,dir:"asc"},selectedRows:new Set,filters:{},loading:!1},this.init()}init(){this.initTouchEvents(),this.createStructure(),this.attachEvents(),this.updateSortIcons(),this.options.autoLoad&&this.options.apiUrl&&(this.loadData(),this.options.responsive&&setTimeout(()=>this.initResponsiveSystem(),100))}createStructure(){const e=document.createElement("div");e.className="easy-datatable-wrapper",this.element.parentNode.insertBefore(e,this.element),e.appendChild(this.element);const t=document.createElement("div");t.className="easy-dt-top d-flex justify-content-between align-items-center mb-3";const n=document.createElement("div");this.options.paging&&(n.innerHTML=`
                <label class="d-flex align-items-center gap-2">
                    ${this.options.language.lengthMenu.replace("_MENU_",`<select class="form-select form-select-sm" style="width: auto;">
                            ${this.options.lengthMenu.map(s=>`<option value="${s}" ${s===this.options.pageLength?"selected":""}>${s}</option>`).join("")}
                        </select>`)}
                </label>
            `),t.appendChild(n);const a=document.createElement("div");a.className="easy-dt-buttons",this.options.buttons&&this.options.buttons.length>0&&this.createButtons(a),t.appendChild(a);const o=document.createElement("div");this.options.searching&&(o.innerHTML=`
                <label class="d-flex align-items-center gap-2">
                    ${this.options.language.search}
                    <input type="search" class="form-control form-control-sm" placeholder="Cari data...">
                </label>
            `),t.appendChild(o),e.insertBefore(t,this.element),this.createTableHeaders(),this.element.querySelector("tbody")||this.element.appendChild(document.createElement("tbody"));const i=document.createElement("div");if(i.className="easy-dt-bottom d-flex justify-content-between align-items-center mt-3",this.options.info){const s=document.createElement("div");s.className="easy-dt-info",i.appendChild(s)}if(this.options.paging){const s=document.createElement("div");s.innerHTML='<nav><ul class="pagination pagination-sm mb-0"></ul></nav>',i.appendChild(s)}if(e.appendChild(i),this.options.processing){const s=document.createElement("div");s.className="easy-dt-processing position-absolute top-50 start-50 translate-middle d-none",s.innerHTML=`
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-spinner fa-spin mb-2"></i>
                    <span class="fw-semibold fs-5">${this.options.language.processing}</span>
                </div>
            `,e.style.position="relative",e.appendChild(s)}this.options.responsive&&this.addResponsiveCSS()}addResponsiveCSS(){const e=document.createElement("style");e.textContent=`
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


        `,document.head.appendChild(e)}createTableHeaders(){let e=this.element.querySelector("thead");e||(e=document.createElement("thead"),this.element.appendChild(e));const t=document.createElement("tr");if(this.options.responsive||this.options.select){const n=document.createElement("th");let a="";this.options.responsive&&(a+=""),this.options.select&&(a+='<input type="checkbox" class="form-check-input select-all">'),n.innerHTML=a,n.style.width=this.options.select?"50px":"30px",t.appendChild(n)}this.options.columns.forEach((n,a)=>{const o=document.createElement("th");o.textContent=n.title||n.data,n.className&&(o.className=n.className),this.options.ordering&&n.orderable!==!1&&(o.classList.add("sortable"),o.style.cursor="pointer",o.setAttribute("data-column",a),o.innerHTML=`${n.title||n.data} <i class="fas fa-sort ms-1 sort-icon"></i>`),t.appendChild(o)}),e.innerHTML="",e.appendChild(t)}attachEvents(){const e=this.element.closest(".easy-datatable-wrapper"),t=e.querySelector("select");t&&t.addEventListener("change",a=>{this.state.pageSize=parseInt(a.target.value),this.state.currentPage=1,this.loadData()});const n=e.querySelector('input[type="search"]');if(n){let a;n.addEventListener("input",o=>{clearTimeout(a),a=setTimeout(()=>{this.state.search=o.target.value,this.state.currentPage=1,this.loadData()},300)})}this.options.ordering&&this.element.addEventListener("click",a=>{const o=a.target.closest("th.sortable");if(o){const i=parseInt(o.getAttribute("data-column")),l=(this.state.order.column===i?this.state.order.dir:"asc")==="asc"?"desc":"asc";this.state.order={column:i,dir:l},this.updateSortIcons(),this.loadData()}}),this.options.select&&e.addEventListener("change",a=>{a.target.classList.contains("select-all")&&(this.element.querySelectorAll('tbody input[type="checkbox"].row-select').forEach(i=>{i.checked=a.target.checked;const s=i.getAttribute("data-id");a.target.checked?this.state.selectedRows.add(s):this.state.selectedRows.delete(s)}),this.triggerSelectEvent())})}async loadData(){if(this.options.apiUrl&&!this.state.loading){this.state.loading=!0,this.showProcessing(!0);try{const{default:e}=await M(async()=>{const{default:s}=await import("./axiosClient-8LtfCK6A.js");return{default:s}},[]),t=this.buildParams(),a=(await e.get(this.options.apiUrl,{params:t})).data;let o,i;Array.isArray(a)?(o=a[0]||{},i=a.slice(1)):(o=a,i=[]),this.renderData(o.data||[]),this.updateInfo(o.meta||{}),this.updatePagination(o.meta||{}),this.options.onDataLoaded&&typeof this.options.onDataLoaded=="function"&&this.options.onDataLoaded({table:o,additional:i,raw:a})}catch(e){if(console.error("EasyDataTable: Error loading data",e),e.response&&e.response.data){const n=e.response.data.message||"An error occurred",a=e.response.status;this.showNotification(`Error ${a}: ${n}`,"error")}else this.showNotification("Network error occurred","error");this.renderError()}finally{this.state.loading=!1,this.showProcessing(!1)}}}buildParams(){const e={page:this.state.currentPage,size:this.state.pageSize,search:this.state.search};if(this.state.filters&&Object.assign(e,this.state.filters),this.options.ordering&&this.state.order){const t=this.options.columns[this.state.order.column];t&&(e.sort_column=t.data,e.sort_dir=this.state.order.dir)}return this.options.data&&typeof this.options.data=="function"&&this.options.data(e),e}renderData(e){const t=this.element.querySelector("tbody");if(t.innerHTML="",!e.length){const n=document.createElement("tr"),a=document.createElement("td");a.colSpan=this.options.columns.length+(this.options.select?1:0),a.textContent=this.options.language.emptyTable,a.className="text-center",n.appendChild(a),t.appendChild(n);return}e.forEach((n,a)=>{const o=document.createElement("tr");if(this.options.responsive||this.options.select){const i=document.createElement("td");let s="";this.options.responsive&&(s+=`<span class="dtr-control me-1 rounded" data-id="${n.id||a}" style="cursor: pointer;"></span>`),this.options.select&&(s+=`<input type="checkbox" class="form-check-input row-select" data-id="${n.id||a}">`),i.innerHTML=s,o.appendChild(i)}this.options.columns.forEach(i=>{const s=document.createElement("td");if(i.className&&(s.className=i.className),i.data==="DT_RowIndex"){const l=(this.state.currentPage-1)*this.state.pageSize+a+1;s.textContent=l,s.className="text-center"}else i.render&&typeof i.render=="function"?s.innerHTML=i.render(n[i.data],"display",n):s.textContent=n[i.data]||"";o.appendChild(s)}),t.appendChild(o)}),this.options.select&&t.querySelectorAll(".row-select").forEach(n=>{n.addEventListener("change",a=>{const o=a.target.getAttribute("data-id");a.target.checked?this.state.selectedRows.add(o):this.state.selectedRows.delete(o),this.updateSelectAllCheckbox(),this.triggerSelectEvent()})}),this.options.responsive&&t.querySelectorAll(".dtr-control").forEach((n,a)=>{n.addEventListener("click",o=>{o.preventDefault(),o.stopPropagation();const i=n.closest("tr"),s=i.nextElementSibling,l=e[a];document.querySelectorAll(".detail-row").forEach(r=>{r!==s&&r.remove()}),document.querySelectorAll(".dtr-control.open").forEach(r=>{r!==n&&r.classList.remove("open")}),s&&s.classList.contains("detail-row")?(s.remove(),n.classList.remove("open")):(this.createDetailRow(i,l),n.classList.add("open"))})}),this.options.responsive&&setTimeout(()=>this.updateResponsiveColumns(),10),this.options.select&&setTimeout(()=>this.updateSelectAllCheckbox(),10)}updateInfo(e){const t=this.element.closest(".easy-datatable-wrapper").querySelector(".easy-dt-info");if(!t)return;const n=e.total||0,a=n>0?(this.state.currentPage-1)*this.state.pageSize+1:0,o=Math.min(this.state.currentPage*this.state.pageSize,n);let i=this.options.language.info.replace("_START_",a).replace("_END_",o).replace("_TOTAL_",n);this.state.search&&(i+=" "+this.options.language.infoFiltered.replace("_MAX_",n)),t.textContent=i}updatePagination(e){const t=this.element.closest(".easy-datatable-wrapper").querySelector(".pagination");if(!t)return;const n=e.total||0,a=Math.ceil(n/this.state.pageSize);if(t.innerHTML="",a<=1)return;const o=document.createElement("li");o.className=`page-item ${this.state.currentPage===1?"disabled":""}`,o.innerHTML=`<a class="page-link" href="#" data-page="1">${this.options.language.paginate.first}</a>`,t.appendChild(o);const i=document.createElement("li");i.className=`page-item ${this.state.currentPage===1?"disabled":""}`,i.innerHTML=`<a class="page-link" href="#" data-page="${this.state.currentPage-1}">${this.options.language.paginate.previous}</a>`,t.appendChild(i);const s=window.innerWidth<=768,l=s?3:7;let r,c;if(a<=l)r=1,c=a;else{const d=Math.floor(l/2),p=Math.ceil(l/2)-1;this.state.currentPage<=d?(r=1,c=l):this.state.currentPage+p>=a?(r=a-l+1,c=a):(r=this.state.currentPage-d,c=this.state.currentPage+p)}if(!s&&r>2){const d=document.createElement("li");if(d.className="page-item",d.innerHTML='<a class="page-link" href="#" data-page="1">1</a>',t.appendChild(d),r>3){const p=document.createElement("li");p.className="page-item disabled",p.innerHTML='<span class="page-link">...</span>',t.appendChild(p)}}else if(!s&&r===2){const d=document.createElement("li");d.className="page-item",d.innerHTML='<a class="page-link" href="#" data-page="1">1</a>',t.appendChild(d)}for(let d=r;d<=c;d++){const p=document.createElement("li");p.className=`page-item ${d===this.state.currentPage?"active":""}`,p.innerHTML=`<a class="page-link" href="#" data-page="${d}">${d}</a>`,t.appendChild(p)}if(!s&&c<a){if(c<a-1){const p=document.createElement("li");p.className="page-item disabled",p.innerHTML='<span class="page-link">...</span>',t.appendChild(p)}const d=document.createElement("li");d.className="page-item",d.innerHTML=`<a class="page-link" href="#" data-page="${a}">${a}</a>`,t.appendChild(d)}const u=document.createElement("li");u.className=`page-item ${this.state.currentPage===a?"disabled":""}`,u.innerHTML=`<a class="page-link" href="#" data-page="${this.state.currentPage+1}">${this.options.language.paginate.next}</a>`,t.appendChild(u);const m=document.createElement("li");m.className=`page-item ${this.state.currentPage===a?"disabled":""}`,m.innerHTML=`<a class="page-link" href="#" data-page="${a}">${this.options.language.paginate.last}</a>`,t.appendChild(m);const g=this.element.closest(".easy-datatable-wrapper").querySelector(".pagination");g&&g._paginationHandler&&g.removeEventListener("click",g._paginationHandler);const x=d=>{d.preventDefault();const p=d.target.closest("a[data-page]");p&&!p.closest(".disabled")&&(this.state.currentPage=parseInt(p.getAttribute("data-page")),this.loadData())};t.addEventListener("click",x),t._paginationHandler=x}updateSortIcons(){this.element.querySelectorAll("th.sortable").forEach((t,n)=>{const a=t.querySelector("i");a&&(a.className="fas ms-1 "+(n===this.state.order.column?this.state.order.dir==="asc"?"fa-sort-up":"fa-sort-down":"fa-sort"))})}showProcessing(e){const t=this.element.closest(".easy-datatable-wrapper").querySelector(".easy-dt-processing");t&&t.classList.toggle("d-none",!e)}renderError(){const e=this.element.querySelector("tbody");e.innerHTML=`
            <tr>
                <td colspan="${this.options.columns.length+(this.options.select?1:0)}" class="text-center text-danger">
                    Error loading data
                </td>
            </tr>
        `}triggerSelectEvent(){const e=new CustomEvent("select",{detail:{selectedRows:Array.from(this.state.selectedRows)}});this.element.dispatchEvent(e)}reload(e={}){this.state.filters=e,this.state.currentPage=1,this.loadData()}search(e){this.state.search=e,this.state.currentPage=1,this.loadData()}page(e){this.state.currentPage=e,this.loadData()}getSelectedRows(){return Array.from(this.state.selectedRows)}clearSelection(){this.state.selectedRows.clear(),this.element.querySelectorAll('input[type="checkbox"]').forEach(t=>t.checked=!1),this.updateSelectAllCheckbox(),this.triggerSelectEvent()}updateSelectAllCheckbox(){const t=this.element.closest(".easy-datatable-wrapper").querySelector(".select-all"),n=this.element.querySelectorAll("tbody .row-select");if(t&&n.length>0){const a=Array.from(n).filter(o=>o.checked).length;a===0?(t.checked=!1,t.indeterminate=!1):a===n.length?(t.checked=!0,t.indeterminate=!1):(t.checked=!1,t.indeterminate=!0)}}updateConfig(e){this.options={...this.options,...e},this.createTableHeaders(),this.loadData()}createButtons(e){const t=document.createElement("div");t.className="d-flex gap-1",this.options.buttons.forEach(n=>{if(typeof n=="string"){const a=this.createBuiltInButton(n);a&&t.appendChild(a)}else if(typeof n=="object"){const a=this.createCustomButton(n);a&&t.appendChild(a)}}),e.appendChild(t)}createBuiltInButton(e){const t=document.createElement("button"),a={copy:{text:"Copy",icon:"fas fa-copy",className:"btn-info",action:()=>this.exportCopy()},csv:{text:"CSV",icon:"fas fa-file-csv",className:"btn-success",action:()=>this.exportCSV()},excel:{text:"Excel",icon:"fas fa-file-excel",className:"btn-warning",action:()=>this.exportExcel()},pdf:{text:"PDF",icon:"fas fa-file-pdf",className:"btn-danger",action:()=>this.exportPDF()},print:{text:"Print",icon:"fas fa-print",className:"btn-secondary",action:()=>this.exportPrint()},colvis:{text:"Columns",icon:"fas fa-columns",className:"btn-info",action:()=>this.toggleColumnVisibility()}}[e];return a?(t.type="button",t.className=`btn ${a.className} btn-sm`,t.innerHTML=`<i class="${a.icon}"></i> <span class="btn-text">${a.text}</span>`,t.addEventListener("click",a.action),t):null}createCustomButton(e){const t=document.createElement("button");return t.type="button",t.className=e.className||"btn btn-outline-secondary btn-sm",t.innerHTML=e.text||"Button",e.id&&(t.id=e.id),e.style&&t.setAttribute("style",e.style),e.action&&typeof e.action=="function"&&t.addEventListener("click",()=>e.action(this)),t}exportCopy(){const e=this.getCurrentTableData(),t=this.formatDataForCopy(e);navigator.clipboard.writeText(t).then(()=>{this.showNotification("Data copied to clipboard","success")}).catch(()=>{this.showNotification("Failed to copy data","error")})}exportCSV(){const e=this.getCurrentTableData(),t=this.formatDataForCSV(e);this.downloadFile(t,"text/csv","export.csv"),this.showNotification("CSV exported successfully","success")}exportExcel(){const e=this.getCurrentTableData(),t=this.formatDataForExcel(e);this.downloadFile(t,"application/vnd.ms-excel","export.xls"),this.showNotification("Excel exported successfully","success")}exportPDF(){const e=this.getCurrentTableData();this.generatePDF(e)}exportPrint(){const e=this.getCurrentTableData(),t=this.formatDataForPrint(e),n=window.open("","_blank","width=800,height=600");n&&(n.document.write(t),n.document.close(),n.onload=()=>{n.focus(),n.print(),setTimeout(()=>n.close(),1e3)})}getCurrentTableData(){const e=Array.from(this.element.querySelectorAll("thead th")).map(n=>n.textContent.trim()),t=Array.from(this.element.querySelectorAll("tbody tr")).map(n=>Array.from(n.querySelectorAll("td")).map(a=>a.textContent.trim()));return{headers:e,rows:t}}formatDataForCopy(e){const t=[e.headers.join("	")];return e.rows.forEach(n=>t.push(n.join("	"))),t.join(`
`)}formatDataForCSV(e){const t=[e.headers.map(n=>`"${n}"`).join(",")];return e.rows.forEach(n=>{t.push(n.map(a=>`"${a}"`).join(","))}),t.join(`
`)}formatDataForExcel(e){let t='<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?>';return t+='<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet">',t+="<Worksheet><Table>",t+="<Row>",e.headers.forEach(n=>{t+=`<Cell><Data ss:Type="String">${n}</Data></Cell>`}),t+="</Row>",e.rows.forEach(n=>{t+="<Row>",n.forEach(a=>{t+=`<Cell><Data ss:Type="String">${a}</Data></Cell>`}),t+="</Row>"}),t+="</Table></Worksheet></Workbook>",t}formatDataForPrint(e){return`
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
                        <tr>${e.headers.map(t=>`<th>${t}</th>`).join("")}</tr>
                    </thead>
                    <tbody>
                        ${e.rows.map(t=>`<tr>${t.map(n=>`<td>${n}</td>`).join("")}</tr>`).join("")}
                    </tbody>
                </table>
            </body>
            </html>
        `}generatePDF(e){this.exportPrint(),this.showNotification('Use browser print dialog and select "Save as PDF"',"info")}downloadFile(e,t,n){const a=new Blob([e],{type:t}),o=window.URL.createObjectURL(a),i=document.createElement("a");i.href=o,i.download=n,document.body.appendChild(i),i.click(),document.body.removeChild(i),window.URL.revokeObjectURL(o)}showNotification(e,t="info"){typeof Swal<"u"?Swal.fire({title:t==="error"?"Error":"Success",text:e,icon:t==="error"?"error":"success",timer:2e3,showConfirmButton:!1}):typeof toastr<"u"?toastr[t](e):alert(e)}initResponsiveSystem(){this.updateResponsiveColumns();let e;window.addEventListener("resize",()=>{clearTimeout(e),e=setTimeout(()=>{this.updateResponsiveColumns()},150)})}updateResponsiveColumns(){if(!this.options.responsive)return;const e=this.getHiddenColumns(),t=e.length>0,n=this.element.querySelectorAll("thead th"),a=this.element.querySelectorAll("tbody tr:not(.detail-row)");n.forEach((i,s)=>{if(s===0)return;const l=s-1;i.style.display=e.includes(l)?"none":""}),a.forEach(i=>{i.querySelectorAll("td").forEach((l,r)=>{if(r===0)return;const c=r-1;l.style.display=e.includes(c)?"none":""})}),this.element.querySelectorAll(".dtr-control").forEach(i=>{i.style.display=t?"inline-flex":"none"}),this.setProportionalWidths(e)}toggleColumnVisibility(){this.showNotification("Gunakan responsive breakpoints untuk mengatur kolom","info")}destroy(){const e=this.element.closest(".easy-datatable-wrapper");if(e){const t=e.parentNode;e._paginationHandler&&e.removeEventListener("click",e._paginationHandler);const n=this.element.id,a=this.element.className,o=this.element.tagName.toLowerCase();e.remove();const i=document.createElement(o);n&&(i.id=n),a&&(i.className=a),t.appendChild(i)}window.masterTable===this&&(window.masterTable=null)}createDetailRow(e,t){const n=document.createElement("tr");n.className="detail-row";const a=document.createElement("td"),o=this.element.querySelectorAll("thead th").length;a.colSpan=o;const i=this.getHiddenColumns();let s='<div class="detail-content">';i.forEach(l=>{const r=this.options.columns[l];if(!r)return;let c=t[r.data]||"";if(r.render&&typeof r.render=="function"&&(c=r.render(c,"display",t)),typeof c=="string"&&c.includes("<")){const u=document.createElement("div");u.innerHTML=c,c=u.textContent||u.innerText||c}s+=`
                <div class="detail-item">
                    <div class="detail-label">${r.title||r.data}</div>
                    <div class="detail-value">${c}</div>
                </div>
            `}),s+="</div>",a.innerHTML=s,n.appendChild(a),e.parentNode.insertBefore(n,e.nextSibling)}getHiddenColumns(){const e=[],t=this.element.closest(".easy-datatable-wrapper").offsetWidth,n=this.estimateColumnWidths();let a=25;return this.options.columns.forEach((o,i)=>{const s=n[i]||100;a+s<t*.98?a+=s:e.push(i)}),e}estimateColumnWidths(){const e=[];return this.options.columns.forEach(t=>{if(t.width){e.push(parseInt(t.width));return}const n=t.title||t.data||"",i=Math.max(60,Math.min(200,n.length*8+40));e.push(i)}),e}setProportionalWidths(e){const t=this.element.querySelectorAll("thead th"),n=this.estimateColumnWidths();let a=30;this.options.columns.forEach((o,i)=>{e.includes(i)||(a+=n[i])}),t.forEach((o,i)=>{if(i===0)o.style.width="30px";else{const s=i-1;if(!e.includes(s)){const l=this.options.columns[s];l&&l.width?o.style.width=l.width:o.style.width="auto"}}})}initTouchEvents(){if("ontouchstart"in window){let e=!1;this.element.addEventListener("touchstart",t=>{const n=t.target.closest(".dtr-control");n&&(e=!0,n.style.transform="scale(0.95)")},{passive:!0}),this.element.addEventListener("touchend",t=>{const n=t.target.closest(".dtr-control");n&&e&&(n.style.transform="",e=!1)},{passive:!0})}}};typeof $<"u"&&($.fn.easyDataTable=function(h){return this.each(function(){return this.easyDataTable||(this.easyDataTable=new w(this,h)),this.easyDataTable})});typeof y<"u"&&y.exports&&(y.exports=w);window.EasyDataTable=w;class I{constructor(e){if(this.options={tableInstance:null,deleteUrl:"",confirmMessage:"Yakin ingin menghapus {count} data terpilih?",buttonId:"btnDeleteSelected",onDeleteSuccess:null,onDeleteError:null,...e},!this.options.tableInstance)throw new Error("EasyTableBulkDelete: tableInstance is required");this.init()}init(){this.attachSelectionListener()}attachSelectionListener(){this.options.tableInstance&&this.options.tableInstance.element&&this.options.tableInstance.element.addEventListener("select",e=>{const t=e.detail.selectedRows.length;this.updateButton(t)})}execute(){const e=this.options.tableInstance.getSelectedRows();if(e.length===0){typeof showToast<"u"?showToast("Pilih data yang akan dihapus!","error"):alert("Pilih data yang akan dihapus!");return}if(typeof Swal<"u")Swal.fire({title:"Apakah Anda yakin?",html:`Anda akan menghapus <strong>${e.length}</strong> data terpilih!`,icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Ya, Hapus!",cancelButtonText:"Tidak"}).then(t=>{t.isConfirmed&&this.performDelete(e)});else{const t=this.options.confirmMessage.replace("{count}",e.length);confirm(t)&&this.performDelete(e)}}async performDelete(e){var t,n;try{typeof Swal<"u"&&Swal.fire({title:"Menghapus...",html:"Silakan tunggu",allowOutsideClick:!1,didOpen:()=>{Swal.showLoading()}});const a=await N.post(this.options.deleteUrl,{ids:e});a.data.status==="success"?(typeof showToast<"u"&&showToast(a.data.message,"success"),this.options.tableInstance.reload(),this.options.tableInstance.clearSelection(),this.options.onDeleteSuccess&&this.options.onDeleteSuccess(e,a.data)):typeof showToast<"u"&&showToast(a.data.message,"error")}catch(a){console.error("Delete failed:",a);const o=((n=(t=a.response)==null?void 0:t.data)==null?void 0:n.message)||"Terjadi kesalahan, silakan coba lagi.";typeof showToast<"u"&&showToast(o,"error")}finally{typeof Swal<"u"&&Swal.close()}}updateButton(e){const t=document.getElementById(this.options.buttonId);t&&(e>0?(t.style.display="inline-block",t.innerHTML=`<i class="fas fa-trash"></i> Hapus ${e}`):t.style.display="none")}destroy(){}}function H(h){return new I(h)}let f;document.addEventListener("DOMContentLoaded",function(){if(!document.querySelector("#table-media")){console.log("Media table not found, skipping DataTable initialization");return}const e={urlWeb:"/media-library/",urlApi:"/api/media-management",deleteMultipleUrl:"/api/media-management/multiple/delete/"},t={selector:"#table-media",pageLength:10,lengthMenu:[10,25,50,100],select:!0,order:{column:1,dir:"desc"},responsive:!0},n=[{data:"DT_RowIndex",title:"No",orderable:!1,searchable:!1},{data:"url",title:"Preview",orderable:!1,searchable:!1,render:(s,l,r)=>r.mime_type&&r.mime_type.startsWith("image/")?`<img src="${s}" alt="Preview" width="40" height="40" 
                            style="object-fit: cover; border-radius: 4px; cursor: pointer;" 
                            onclick="showMediaPreview('${s}', '${r.mime_type}', '${r.name}')">`:'<i class="fas fa-file text-muted" style="font-size: 24px;"></i>'},{data:"name",title:"Name",orderable:!0},{data:"file_name",title:"File",orderable:!0},{data:"collection",title:"Collection",orderable:!1,render:s=>`<span class="badge bg-primary">${s||"default"}</span>`},{data:"mime_type",title:"Type",orderable:!1,render:s=>{const l=s?s.split("/")[0]:"unknown";return`<span class="badge bg-${{image:"success",video:"info",audio:"warning",application:"secondary",text:"dark"}[l]||"secondary"}">${l}</span>`}},{data:"size",title:"Size",orderable:!0},{data:"model_type",title:"Model",orderable:!1,render:(s,l,r)=>`${s||"Media"} #${r.model_id||r.id}`},{data:"created_at",title:"Created",orderable:!0,render:s=>new Date(s).toLocaleDateString("id-ID")},{data:"actions",title:"Action",className:"text-center",orderable:!1,searchable:!1,render:(s,l,r)=>s||""}],a=[{id:"btnTambah",text:'<i class="fas fa-plus"></i> Add Media',className:"btn btn-primary btn-sm me-1",style:"display: none;",action:()=>b(`${e.urlWeb}create`,"create")},{id:"btnDeleteSelected",text:'<i class="fas fa-trash"></i> Delete',className:"btn btn-danger btn-sm me-1",style:"display: none;",action:()=>i==null?void 0:i.execute()},"copy","csv","excel","pdf","print"];f=new EasyDataTable(t.selector,{apiUrl:e.urlApi,columns:n,pageLength:t.pageLength,lengthMenu:t.lengthMenu,select:t.select,order:t.order,responsive:t.responsive,buttons:a,onDataLoaded:s=>{var l;o((l=s.meta)==null?void 0:l.permissions)}});function o(s){const l=document.querySelector("#btnTambah"),r=document.querySelector("#btnDeleteSelected"),c=s||{};l&&(c.create?(l.style.display="block",l.disabled=!1):(l.style.display="none",l.disabled=!0)),r&&(c.delete?(r.style.display="none",r.disabled=!1):(r.style.display="none",r.disabled=!0)),window.currentPermissions=c}P({buttonSelector:".btn-delete",deleteUrl:"/api/media-management",tableSelector:t.selector,onDeleteSuccess:()=>{f.reload(),f.clearSelection()}});const i=H({tableInstance:f,deleteUrl:e.deleteMultipleUrl,confirmMessage:"Yakin ingin menghapus {count} media terpilih?",onDeleteSuccess:s=>console.log("Bulk delete berhasil:",s)});document.body.addEventListener("click",s=>{const l=s.target.closest(".buttonUpdate"),r=s.target.closest(".buttonShow"),c=s.target.matches("#btnAction");if(s.target.closest(".btnTambah"))s.preventDefault(),b(`${e.urlWeb}create`,"create");else if(l){s.preventDefault();const m=`${e.urlWeb}${l.dataset.id}/edit`;b(m,"edit")}else if(r){s.preventDefault();const m=`${e.urlWeb}${r.dataset.id}`;b(m,"show")}else if(c){s.preventDefault();const m=document.querySelector(".FormAction");m&&A({url:m.action,method:m.method,data:new FormData(m)},"simpan",()=>f.reload())}})});window.showMediaPreview=function(h,e,t){const n=new bootstrap.Modal(document.getElementById("mediaPreviewModal")),a=document.getElementById("media-preview-content");e.startsWith("image/")?a.innerHTML=`<img src="${h}" alt="${t}" class="img-fluid" style="max-height: 500px;">`:e.startsWith("video/")?a.innerHTML=`<video controls class="w-100" style="max-height: 500px;">
                               <source src="${h}" type="${e}">
                               Browser Anda tidak mendukung video.
                             </video>`:e.startsWith("audio/")?a.innerHTML=`<audio controls class="w-100">
                               <source src="${h}" type="${e}">
                               Browser Anda tidak mendukung audio.
                             </audio>`:a.innerHTML=`<div class="text-center">
                               <i class="fas fa-file fa-5x text-muted mb-3"></i>
                               <p>File: ${t}</p>
                               <a href="${h}" target="_blank" class="btn btn-primary">
                                 <i class="fas fa-download"></i> Download
                               </a>
                             </div>`,n.show()};document.addEventListener("DOMContentLoaded",async function(){const h=document.getElementById("mediaGrid"),e=document.getElementById("uploadArea"),t=document.getElementById("table-media");if(h||e)try{window.mediaLibrary=new v}catch(n){console.error("Failed to initialize MediaLibrary:",n)}t&&console.log("Initializing media DataTable..."),C(),window.MediaLibrary=v,window.MediaUpload=k,window.ImageCropper=S,window.MediaPicker=D,window.ContextMenu=T,window.openMediaPicker=L})});export default _();
