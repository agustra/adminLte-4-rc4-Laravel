import axiosClient from "@api/axiosClient.js";

export function handleTomSelectModal(
    url,
    modalId,
    inputValue,
    options = {},
    callback
) {
    // Jika options adalah function, berarti callback
    if (typeof options === "function") {
        callback = options;
        options = {};
    }

    const config = {
        submitUrl: options.submitUrl || null,
        fieldMapping: options.fieldMapping || {},
        buttonId: options.buttonId || "btnActionTomSelect",
        ...options,
    };

    axiosClient
        .get(url)
        .then((response) => {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.querySelector(".modal-dialog").innerHTML = response.data;

            // Pre-fill input dengan nilai dari TomSelect
            const nameInput = modal.querySelector(
                'input[name="name"], input[name="role"]'
            );
            if (nameInput && inputValue) {
                nameInput.value = inputValue;
            }

            // Ubah ID button agar tidak bentrok
            const btnAction = modal.querySelector("#btnAction");
            if (btnAction) {
                btnAction.id = config.buttonId;
            }

            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            const form = modal.querySelector("form");

            if (form) {
                form.addEventListener("submit", function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);

                    // Apply field mapping jika ada
                    Object.keys(config.fieldMapping).forEach((oldField) => {
                        if (formData.has(oldField)) {
                            const value = formData.get(oldField);
                            formData.delete(oldField);
                            formData.set(config.fieldMapping[oldField], value);
                        }
                    });
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }

                    // Tentukan URL submit
                    const submitUrl = config.submitUrl || form.action;

                    axiosClient
                        .post(submitUrl, formData)
                        .then((res) => {
                            showToast(res.data.message, "success");
                            if (callback) callback(res.data);
                            modalInstance.hide();
                        })
                        .catch((err) => {
                            console.error("Submit error:", err);
                            console.error(
                                "Error response:",
                                err.response?.data
                            );
                            console.error(
                                "Validation errors:",
                                err.response?.data?.message
                            );
                            console.error(
                                "Error status:",
                                err.response?.status
                            );
                        });
                });
            }
        })
        .catch((err) => console.error(err));
}
