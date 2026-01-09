document.addEventListener("DOMContentLoaded", () => {
    initCrud();
});

function initCrud() {
    const table = document.querySelector(".crud-table");
    const apiUrl = table?.dataset.apiUrl;
    const modal = document.querySelector("#crudModal");
    const form = document.querySelector("#crudForm");

    if (!table || !apiUrl) {
        console.warn("CRUD: Missing table or API URL");
        return;
    }

    // Fetch initial data
    fetchData(apiUrl, table);

    // Add record
    const addBtn = document.querySelector("#addBtn");
    if (addBtn && modal && form) {
        addBtn.addEventListener("click", () => {
            form.reset();
            form.dataset.id = "";
            // remove hidden _method if it exists
            const oldMethod = form.querySelector('input[name="_method"]');
            if (oldMethod) oldMethod.remove();
            $(modal).modal("show");
        });
    }

    // Submit form (Add / Update)
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const id = form.dataset.id;
            const isEdit = !!id;
            const url = isEdit ? `${apiUrl}/${id}` : apiUrl;

            const formData = new FormData(form);
            // Laravel expects _method for PUT/PATCH
            if (isEdit) {
                formData.append("_method", "PUT");
            }

            try {
                const response = await fetch(url, {
                    method: "POST", // always POST for Laravel web routes
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Accept": "application/json",
                    },
                    body: formData,
                });

                const result = await response.json();

                if (!response.ok) {
                    console.error("Save failed:", result);
                    alert("Error: " + (result.message || "Something went wrong"));
                    return;
                }

                $(modal).modal("hide");
                fetchData(apiUrl, table);
            } catch (error) {
                console.error("CRUD save error:", error);
            }
        });
    }
}

// --- Fetch Data ---
async function fetchData(apiUrl, table) {
    try {
        const response = await fetch(apiUrl);
        const data = await response.json();

        const tbody = table.querySelector("tbody");
        tbody.innerHTML = "";

        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="100%" class="text-center">No Data Found</td></tr>`;
            return;
        }

        data.forEach((item, index) => {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.name ?? "-"}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${item.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${item.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML("beforeend", row);
        });

        attachRowActions(apiUrl, table);
    } catch (error) {
        console.error("CRUD fetch error:", error);
    }
}

// --- Edit & Delete Buttons ---
function attachRowActions(apiUrl, table) {
    const modal = document.querySelector("#crudModal");
    const form = document.querySelector("#crudForm");

    // Edit
    table.querySelectorAll(".edit-btn").forEach((btn) =>
        btn.addEventListener("click", async () => {
            const id = btn.dataset.id;
            const response = await fetch(`${apiUrl}/${id}`);
            const data = await response.json();

            Object.keys(data).forEach((key) => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) input.value = data[key];
            });

            form.dataset.id = id;

            // ensure hidden _method input exists for Laravel PUT
            let methodField = form.querySelector('input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement("input");
                methodField.type = "hidden";
                methodField.name = "_method";
                form.appendChild(methodField);
            }
            methodField.value = "PUT";

            $(modal).modal("show");
        })
    );

    // Delete
    table.querySelectorAll(".delete-btn").forEach((btn) =>
        btn.addEventListener("click", async () => {
            const id = btn.dataset.id;
            if (!confirm("Are you sure you want to delete this record?")) return;
            await fetch(`${apiUrl}/${id}`, {
                method: "POST", // Laravel web route delete via POST + _method=DELETE
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: new URLSearchParams({ _method: "DELETE" }),
            });
            fetchData(apiUrl, table);
        })
    );
}
