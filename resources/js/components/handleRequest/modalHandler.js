import axiosClient from "@api/axiosClient.js";

// Fungsi untuk mengatur tampilan modal berdasarkan actionType
function configureModalByAction(modal, actionType) {
    const modalHeader = modal.querySelector(".modal-header");
    const btnAction = modal.querySelector("#btnAction");
    const modalTitle = modal.querySelector(".modal-title");

    if (!modalHeader || !btnAction || !modalTitle) return;

    // Reset ke kondisi default
    modalHeader.className = "modal-header";
    modalHeader.style.display = "flex";
    // btnAction.className = "btn"; // Reset ke class dasar tombol
    btnAction.style.display = "block";

    // Konfigurasi modal berdasarkan tipe aksi
    const config = {
        create: {
            headerClass: ["gradient-card"],
            btnClass: ["gradient-card"],
            btnText: "💾 Save",
            title: "🗂️ Create Data",
        },
        edit: {
            headerClass: ["gradient-card"],
            btnClass: [],
            btnText: "💾 Update",
            title: "🗂️ Update Data",
        },
        show: {
            headerClass: [],
            btnClass: [],
            btnText: "",
            title: "",
        },
    };

    const settings = config[actionType] || config.show;

    // Atur modal sesuai actionType
    if (actionType === "show") {
        modalHeader.style.display = "none";
        btnAction.style.display = "none";
    } else {
        if (settings.headerClass.length)
            modalHeader.classList.add(...settings.headerClass);

        if (settings.btnClass.length)
            btnAction.classList.add(...settings.btnClass);

        btnAction.innerHTML = settings.btnText;
        modalTitle.innerHTML = settings.title;
    }
}

// Menangani response sukses dengan menampilkan modal
function handleFetchModal(data, actionType, callback) {
    const modal = document.querySelector("#modalAction");
    if (!modal) return showToast("❌ Modal tidak ditemukan!", "error");

    if (typeof data === "object" && data.status === "error") {
        return showToast(data.msg || "Terjadi kesalahan", "error");
    }

    if (typeof data !== "string") {
        return showToast("Response tidak valid", "error");
    }

    // Update konten modal
    modal.querySelector(".modal-dialog").innerHTML = data;
    modal.classList.add("show");
    modal.style.display = "block";

    // Tunggu DOM ter-update, lalu atur konfigurasi modal
    setTimeout(() => configureModalByAction(modal, actionType), 100);

    // Inisialisasi atau tampilkan modal Bootstrap
    let modalBootstrap = bootstrap.Modal.getInstance(modal);
    if (!modalBootstrap) modalBootstrap = new bootstrap.Modal(modal);
    modalBootstrap.show();

    // Jalankan callback jika ada
    if (typeof callback === "function") callback();
}

export function closeModal() {
    // Bootstrap 5 modal close
    const activeModal = document.querySelector(".modal.show");
    if (activeModal) {
        const modalInstance = bootstrap.Modal.getInstance(activeModal);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    // Fallback: close any visible modal
    document.querySelectorAll(".modal").forEach((modal) => {
        modal.style.display = "none";
        modal.classList.remove("show");
    });

    // Remove backdrop
    document.querySelectorAll(".modal-backdrop").forEach((backdrop) => {
        backdrop.remove();
    });

    // Reset body
    document.body.classList.remove("modal-open");
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";
}

// Fungsi utama untuk mengambil data dan menampilkan modal
export const showModal = async (url = "", actionType, callback = null) => {
    try {
        const response = await axiosClient.get(url);
        handleFetchModal(response.data, actionType, callback);
        return response.data;
    } catch (error) {
        console.error("Error fetching data:", error.message);
        showToast(`Error: ${error.message}`, "error");
        throw error;
    }
};
