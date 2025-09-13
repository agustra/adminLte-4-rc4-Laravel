import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.bootstrap5.css";

import axiosClient from "@api/axiosClient.js";
import { handleTomSelectModal } from "@forms/TomSelectModal.js";

class InitTomSelect {
    constructor(selector, options = {}) {
        const defaultOptions = {
            valueField: "id",
            labelField: "name",
            searchField: ["name"],
            plugins: ["virtual_scroll"],
            maxOptions: 200,
            closeAfterSelect: false,
            create: false,
            multiple: false,
            urlGet: null,
            urlStore: null,
            modalId: null,
            createUrl: null,
            createSingle: false, // default false
        };

        this.settings = { ...defaultOptions, ...options };
        this.selector = selector;
        this.ts = null;
        this.createCallback = null;

        this.init();
    }

    init() {
        const self = this;

        const tomSelectConfig = {
            valueField: this.settings.valueField,
            labelField: this.settings.labelField,
            searchField: this.settings.searchField,
            plugins: this.settings.plugins,
            maxOptions: this.settings.maxOptions,
            closeAfterSelect: this.settings.closeAfterSelect,
            multiple: this.settings.multiple,
        };

        // Add optgroup support if grouped is enabled
        if (this.settings.grouped) {
            tomSelectConfig.optgroupField = "optgroup";
            tomSelectConfig.lockOptgroupOrder = true;
        }

        this.ts = new TomSelect(this.selector, {
            ...tomSelectConfig,
            loadThrottle: 250, // Add throttle for better search performance

            create: this.settings.create
                ? function (input, callback) {
                      if (self.settings.createSingle && self.settings.modalId) {
                          const modalEl = document.getElementById(
                              self.settings.modalId
                          );

                          // Load form create via TomSelectModal
                          const modalConfig = {
                              submitUrl: self.settings.urlStore,
                              ...self.settings.modalOptions,
                          };

                          handleTomSelectModal(
                              self.settings.createUrl,
                              self.settings.modalId,
                              input,
                              modalConfig,
                              (data) => {
                                  if (
                                      data.status === "success" &&
                                      data.results
                                  ) {
                                      // Tambahkan item baru ke TomSelect
                                      const newItem = {
                                          id: data.results.id,
                                          name: data.results.name,
                                      };
                                      if (callback) callback(newItem);
                                  }
                              }
                          );
                      }

                      // Jika createSingle false, langsung create via API
                      else if (
                          !self.settings.createSingle &&
                          self.settings.urlStore
                      ) {
                          axiosClient
                              .post(self.settings.urlStore, { name: input })
                              .then((res) => {
                                  if (
                                      res.data.status === "success" &&
                                      res.data.results
                                  ) {
                                      const newItem = {
                                          id: res.data.results.id,
                                          name: res.data.results.name,
                                      };

                                      // For grouped data, determine optgroup
                                      if (self.settings.grouped) {
                                          const parts = newItem.name.split(" ");
                                          const category =
                                              parts.length > 1
                                                  ? parts[1]
                                                  : "other";
                                          newItem.optgroup =
                                              category.charAt(0).toUpperCase() +
                                              category.slice(1);

                                          // Add optgroup if not exists
                                          if (
                                              !this.optgroups[newItem.optgroup]
                                          ) {
                                              this.addOptionGroup(
                                                  newItem.optgroup,
                                                  {
                                                      label: newItem.optgroup,
                                                      value: newItem.optgroup,
                                                  }
                                              );
                                          }
                                      }

                                      showToast(res.data.message, "success");
                                      callback(newItem);
                                  } else {
                                      callback();
                                  }
                              })
                              .catch((err) => {
                                  console.error("Create gagal:", err);
                                  callback();
                              });
                      }
                  }
                : false,

            firstUrl: function (query) {
                // Parse existing URL to preserve parameters like merek_id
                const baseUrl = new URL(
                    self.settings.urlGet,
                    window.location.origin
                );

                // Add search and pagination parameters
                baseUrl.searchParams.set("search", query || "");
                baseUrl.searchParams.set("page", "1");
                baseUrl.searchParams.set("limit", "10");

                return baseUrl.toString();
            },
            load: function (query, callback) {
                const url = this.getUrl(query);
                // console.log('🔗 Load URL:', url);

                axiosClient
                    .get(url)
                    .then((res) => {
                        // console.log("📥 Response:", res.data);
                        const responseData = res.data || {};
                        let data, has_more;

                        // Always use the response format as-is
                        data = responseData.data;
                        has_more = responseData.has_more || false;

                        // handle pagination
                        if (has_more) {
                            const u = new URL(url, window.location.origin);
                            const nextPage =
                                parseInt(
                                    u.searchParams.get("page") || "1",
                                    10
                                ) + 1;
                            u.searchParams.set("page", nextPage);
                            this.setNextUrl(
                                query,
                                `${u.pathname}?${u.searchParams.toString()}`
                            );
                        } else {
                            this.setNextUrl(query, null);
                        }
                        callback(data);

                        let options = [];
                        if (
                            self.settings.grouped &&
                            Array.isArray(data) &&
                            data.length > 0 &&
                            data[0].label &&
                            data[0].options
                        ) {
                            // Handle grouped data: [{label: "Users", options: [...]}]
                            data.forEach((group) => {
                                if (group.label) {
                                    this.addOptionGroup(group.label, {
                                        label: group.label,
                                        value: group.label,
                                    });
                                }
                            });

                            data.forEach((group) => {
                                if (
                                    group.options &&
                                    Array.isArray(group.options)
                                ) {
                                    group.options.forEach((option) => {
                                        options.push({
                                            id: option.id,
                                            name: option.name,
                                            optgroup: group.label,
                                        });
                                    });
                                }
                            });
                        } else {
                            // Handle normal data: [{id: 1, name: "..."}]
                            options = Array.isArray(data) ? data : [];
                        }
                        callback(options);
                    })
                    .catch(() => callback());
            },

            onFocus: function () {
                this.clearOptions();
                if (typeof this.settings.firstUrl === "function") {
                    const url = this.settings.firstUrl("");
                    this.setNextUrl("", url);
                }
                this.load("", () => {});
            },

            onItemAdd: function () {
                this.setTextboxValue("");
                this.refreshOptions(false);
            },

            render: {
                loading_more: function () {
                    return '<div class="py-2 text-center">Loading more…</div>';
                },
                no_more_results: function () {
                    return '<div class="py-2 text-center">No more results</div>';
                },
                option: function (d, esc) {
                    return `<div>${esc(d.name)}</div>`;
                },
                item: function (d, esc) {
                    return `<div>${esc(d.name)}</div>`;
                },
                option_create: function (data, escape) {
                    return `<div class="create">+ Tambah "${escape(
                        data.input
                    )}"</div>`;
                },
                optgroup_header: function (data, escape) {
                    const colors = [
                        "primary",
                        "secondary",
                        "success",
                        "danger",
                        "warning",
                        "info",
                        "dark",
                    ];
                    // Use cryptographically secure random number generator
                    let randomIndex;
                    if (typeof crypto !== 'undefined' && crypto.getRandomValues) {
                        const array = new Uint32Array(1);
                        crypto.getRandomValues(array);
                        randomIndex = array[0] % colors.length;
                    } else {
                        // Fallback for older browsers
                        randomIndex = Math.floor(Math.random() * colors.length);
                    }
                    const randomColor = colors[randomIndex];
                    const label = escape(data.label || data);
                    return `<div class="optgroup-header">
                        <span class="badge bg-${randomColor} me-2">${label.charAt(
                        0
                    )}</span>
                        ${label}
                    </div>`;
                },
            },
        });

        // Auto-select untuk edit mode
        if (
            this.settings.preSelectedValues &&
            this.settings.preSelectedValues.length > 0
        ) {
            // console.log(
            //     "🔄 Auto-selecting values:",
            //     this.settings.preSelectedValues
            // );

            if (this.settings.urlByIds) {
                // 🔹 Cara efisien: hanya ambil data sesuai preSelectedValues
                axiosClient
                    .get(this.settings.urlByIds, {
                        params: { ids: this.settings.preSelectedValues },
                    })
                    .then((res) => {
                        // console.log("📥 API Response (byIds):", res.data);
                        const selectedOptions = res.data.data || [];

                        selectedOptions.forEach((option) => {
                            // console.log("➕ Adding option:", option);
                            this.ts.addOption(option);
                            this.ts.addItem(option.id, true);
                            // console.log("✅ Added and selected:", option.id);
                        });
                    })
                    .catch((err) =>
                        console.error(
                            "❌ Error loading selected items (byIds):",
                            err
                        )
                    );
            } else {
                // 🔹 Fallback: tetap pakai urlGet (kurang efisien untuk data besar)
                axiosClient
                    .get(this.settings.urlGet)
                    .then((res) => {
                        // console.log("📥 API Response (urlGet):", res.data);
                        const allOptions = res.data.data || [];

                        const selectedOptions = allOptions.filter((option) =>
                            this.settings.preSelectedValues.includes(
                                option.id.toString()
                            )
                        );

                        selectedOptions.forEach((option) => {
                            // console.log("➕ Adding option:", option);
                            this.ts.addOption(option);
                            this.ts.addItem(option.id, true);
                            // console.log("✅ Added and selected:", option.id);
                        });
                    })
                    .catch((err) =>
                        console.error(
                            "❌ Error loading selected items (urlGet):",
                            err
                        )
                    );
            }
        }
    }

    addNewItem(data) {
        this.ts.addOption(data);
        this.ts.addItem(data.id);
        if (this.createCallback) {
            this.createCallback(data);
            this.createCallback = null;
        }
    }
}

export default InitTomSelect;
