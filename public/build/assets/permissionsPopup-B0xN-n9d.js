import{_ as b}from"./preload-helper-BfFHrpNk.js";function h(s,i,t="role"){if(!s||!Array.isArray(s)||s.length===0)return'<span class="text-muted">No permissions</span>';const n=s.length,l=i.id,a=i.name;return n<=3?s.map(e=>`<span class="badge bg-primary me-1">${e.name||e}</span>`).join(""):`
            ${s.slice(0,3).map(o=>`<span class="badge bg-primary me-1">${o.name||o}</span>`).join("")}
            <button class="btn btn-sm btn-outline-secondary show-permissions-btn" 
                    data-item-id="${l}"
                    data-item-name="${a}"
                    data-item-type="${t}">
                +${n-3} more
            </button>
        `}function x(){document.body.hasAttribute("data-permissions-popup-initialized")||(document.body.addEventListener("click",function(s){if(s.target.classList.contains("show-permissions-btn")){s.preventDefault(),s.stopPropagation();const i=s.target.dataset.itemId,t=s.target.dataset.itemName,n=s.target.dataset.itemType;u(i,t,n)}}),document.body.setAttribute("data-permissions-popup-initialized","true"))}async function u(s,i,t="role"){const a=`
        <div class="modal fade" id="permissionsModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas ${t==="user"?"fa-user":"fa-shield-alt"} me-2"></i>Permissions for ${t==="user"?"User":"Role"}: <strong>${i}</strong>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div id="permissionsContent">
                            <div class="text-center p-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                <div>Loading permissions...</div>
                            </div>
                        </div>
                        <div id="paginationContainer"></div>
                        <div id="permissionsSummary" class="mt-4" style="display: none;">
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span id="totalInfo"></span>
                                </small>
                            </div>
                        </div>
                    </div>
                    <style>
                    .permission-card {
                        transition: all 0.3s ease;
                        border: 1px solid #e9ecef !important;
                    }
                    .permission-card:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        border-color: #007bff !important;
                    }
                    .permission-list {
                        max-height: 200px;
                        overflow-y: auto;
                    }
                    .permission-list::-webkit-scrollbar {
                        width: 4px;
                    }
                    .permission-list::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 2px;
                    }
                    .permission-list::-webkit-scrollbar-thumb {
                        background: #c1c1c1;
                        border-radius: 2px;
                    }
                    </style>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `,e=document.getElementById("permissionsModal");e&&e.remove(),document.body.insertAdjacentHTML("beforeend",a),new bootstrap.Modal(document.getElementById("permissionsModal")).show(),await c(s,t,1,6),document.getElementById("permissionsModal").addEventListener("hidden.bs.modal",function(){this.remove()})}async function c(s,i,t,n){try{const{default:l}=await b(async()=>{const{default:o}=await import("./axiosClient-8LtfCK6A.js");return{default:o}},[]),a=i==="user"?`/api/users/${s}/permissions/paginated`:`/api/roles/${s}/permissions/paginated`,e=await l.get(a,{params:{page:t,limit:n}});g(e.data,t,n,s,i)}catch(l){console.error("Error loading permissions:",l),document.getElementById("permissionsContent").innerHTML=`
            <div class="text-center p-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <div>Error loading permissions</div>
            </div>
        `}}function g(s,i,t,n,l){const{modules:a,total:e,totalPages:o,totalModules:r}=s,m={users:"bi-people-fill",roles:"bi-shield-fill-check",permissions:"bi-key-fill",menus:"bi-list-ul",media:"bi-image-fill",backup:"bi-archive-fill",settings:"bi-gear-fill",general:"bi-folder2-open"},p=`
        <div class="row g-3">
            ${a.map(d=>`
                <div class="col-md-6 col-lg-4">
                    <div class="permission-card border rounded-3 p-3 h-100 bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <i class="${m[d.name]||m.general} text-warning me-2" style="font-size: 1.2rem;"></i>
                            <h6 class="mb-0 fw-bold text-dark">${d.name.charAt(0).toUpperCase()+d.name.slice(1)}</h6>
                            <span class="badge bg-primary ms-auto">${d.permissions.length}</span>
                        </div>
                        <div class="permission-list">
                            ${d.permissions.map(f=>`
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 0.8rem;"></i>
                                    <span class="text-muted small">${f.name}</span>
                                </div>
                            `).join("")}
                        </div>
                    </div>
                </div>
            `).join("")}
        </div>
    `;document.getElementById("permissionsContent").innerHTML=p,o>1&&v(i,o,n,l,t),document.getElementById("totalInfo").textContent=`Total: ${e} permissions across ${r} modules`,document.getElementById("permissionsSummary").style.display="block"}function v(s,i,t,n,l){let a='<nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">';a+=`
        <li class="page-item ${s===1?"disabled":""}">
            <a class="page-link" href="#" data-page="${s-1}">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;for(let e=1;e<=i;e++)a+=`
            <li class="page-item ${e===s?"active":""}">
                <a class="page-link" href="#" data-page="${e}">${e}</a>
            </li>
        `;a+=`
        <li class="page-item ${s===i?"disabled":""}">
            <a class="page-link" href="#" data-page="${s+1}">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `,a+="</ul></nav>",document.getElementById("paginationContainer").innerHTML=a,document.querySelectorAll("#paginationContainer .page-link").forEach(e=>{e.addEventListener("click",async o=>{o.preventDefault();const r=parseInt(o.target.closest("a").dataset.page);r&&r!==s&&r>=1&&r<=i&&(document.getElementById("permissionsContent").innerHTML=`
                    <div class="text-center p-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <div>Loading permissions...</div>
                    </div>
                `,await c(t,n,r,l))})})}export{h as f,x as i};
