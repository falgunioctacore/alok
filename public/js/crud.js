$(document).ready(function () {
    const $table = $(".crud-table");
    const apiUrl = $table.data("api-url");
    const $tbody = $table.find("tbody");
    const $modal = $("#crudModal");
    const $form = $("#crudForm");
    const $addBtn = $("#addBtn");
    let editingId = null;
   let dt = $table.DataTable();
    // Fetch data
    function fetchData() {
        $.get(apiUrl, function (res) {

            dt.clear(); // Clear existing rowsa

            if (!res || res.length === 0) {
                dt.row.add([
                    "",
                    `<span class="text-muted">No data available</span>`
                ]).draw();
                return;
            }

            res.forEach((item, index) => {

                let actionHtml = "";

                if (item.emp_name) {
                    actionHtml += `
                        <a class="btn btn-sm btn-primary" href="employees/${item.id}/show-page">
                            <i class="fa fa-eye"></i>
                        </a> `;
                }

                if (item.vehicle_no) {
                    actionHtml += `
                        <a class="btn btn-sm btn-primary" href="vehicles/${item.id}/show-page">
                            <i class="fa fa-eye"></i>
                        </a> `;
                }

                actionHtml += `
                    <button class="btn btn-sm btn-warning editBtn" data-id="${item.id}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">
                        <i class="fa fa-trash"></i>
                    </button>
                `;

                // Collect table column values using data-field attributes
                let rowData = [index + 1]; // serial number

                $table.find("thead th[data-field]").each(function () {
                    const field = $(this).data("field");
                    if (field === "pass_no") {
                           item[field] = 'ASVP' + ('0000' + item[field]).slice(-4);
                    }

                    rowData.push(item[field] ?? "");
                });

                rowData.push(actionHtml); // action column

                dt.row.add(rowData);
            });

            dt.draw(); // Refresh DataTable
        });
    }

    fetchData();

    // Add button
    $addBtn.click(function () {
        editingId = null;
        $form.trigger("reset");
        $("#crudModalLabel").text("Add Vehicle");
        $modal.modal("show");
    });

    // Edit button
    $tbody.on("click", ".editBtn", function () {
        const id = $(this).data("id");
        $.get(`${apiUrl}/${id}`, function (data) {
            editingId = id;
            Object.keys(data).forEach(key => {
                //console.log (`[name="${key}"]`)
                $form.find(`[name="${key}"]`).val(data[key]);
            });            
            $("#crudModalLabel").text("Edit Vehicle");
            $modal.modal("show");
        });
    });

    // Delete button
    $tbody.on("click", ".deleteBtn", function () {
        const id = $(this).data("id");
        if (!confirm("Are you sure you want to delete this record?")) return;

        $.ajax({
            url: `${apiUrl}/${id}`,
            type: "DELETE",
            data: { _token: $('meta[name="csrf-token"]').attr("content") },
            success: function(){
                  $("#alertBox").html(`
                  <div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> Deleted!</h5>
                      Deleted successfully!
                  </div>
              `);
                fetchData();
            },
            error: (xhr) => console.error("Delete Error:", xhr.responseText),
        });
    });

    // Form submit (Add / Update)
    // $form.submit(function (e) {
    //     e.preventDefault();
    //     const formData = $form.serialize();
    //     const method = editingId ? "PUT" : "POST";
    //     const url = editingId ? `${apiUrl}/${editingId}` : apiUrl;

    //     $.ajax({
    //         url,
    //         type: method,
    //         data: formData,
    //         success: function () {
    //             $modal.modal("hide");
    //             fetchData();
    //         },
    //         error: function (xhr) {
    //             console.error("Save Error:", xhr.responseText);
    //         },
    //     });
    // });
    $form.submit(function (e) {
    e.preventDefault();

    $("#errorBox").addClass("d-none").html(""); // reset error box

    const formData = $form.serialize();
    const method = editingId ? "PUT" : "POST";
    const url = editingId ? `${apiUrl}/${editingId}` : apiUrl;

    $.ajax({
        url,
        type: method,
        data: formData,
        success: function () {
             $("#alertBox").html(`
                  <div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                      <h5><i class="icon fas fa-check"></i> Success!</h5>
                      ${editingId ? "Updated" : "Added"} successfully!
                  </div>
              `);
            $modal.modal("hide");
            fetchData();
        },
        error: function (xhr) {
            console.error("Save Error:", xhr.responseText);

            let msg = "Something went wrong";

            // Laravel style validation error
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                console.log(errors);
                msg = "";
                $.each(errors, function (key, val) {
                    msg += `<div>${val[0]}</div>`;
                });
            }

            $("#errorBox").removeClass("d-none").html(msg);
        }
    });
});

});

function extractRows(json) {
    if (!json) return [];
    if (Array.isArray(json)) return json;
    if (Array.isArray(json.data)) return json.data;
    if (Array.isArray(json.items)) return json.items;
    if (Array.isArray(json.records)) return json.records;
    if (Array.isArray(json.rows)) return json.rows;
    if (json.result && Array.isArray(json.result)) return json.result;
    return [];
}

document.getElementById('exportExcel').addEventListener('click', async function () {

    // Get table
    let table = document.querySelector(".crud-table");
    if (!table) {
        alert("Table not found!");
        return;
    }

    // Get API URL
    let apiUrl = table.dataset.apiUrl;
    if (!apiUrl) {
        alert("API URL missing!");
        return;
    }

    // Fetch all records
    let response = await fetch(apiUrl + "?all=1");
    let data = await response.json();

    if (!Array.isArray(data) || data.length === 0) {
        alert("No data found!");
        return;
    }

    // Build headers and fields
    let headers = [];
    let fields = [];

    table.querySelectorAll("thead th").forEach(th => {
        let field = th.dataset.field;
        if (!field) return; // skip # and Actions
        headers.push(th.innerText.trim());
        fields.push(field);
    });

    // Build export rows dynamically
    let exportData = data.map(row => {
        let obj = {};
        fields.forEach((field, index) => {
            obj[headers[index]] = row[field] ?? "";
        });
        return obj;
    });

    // Generate Excel Sheet
    let ws = XLSX.utils.json_to_sheet(exportData);
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Export");

    XLSX.writeFile(wb, "export.xlsx");
});


document.getElementById('exportPDF').addEventListener('click', async function () {

    // Get table
    let table = document.querySelector(".crud-table");
    if (!table) {
        alert("Table not found!");
        return;
    }

    // API URL for fetching all data
    let apiUrl = table.dataset.apiUrl;
    if (!apiUrl) {
        alert("API URL missing!");
        return;
    }

    // Fetch full data
    let response = await fetch(apiUrl + "?all=1");
    let data = await response.json();

    if (!Array.isArray(data) || data.length === 0) {
        alert("No records found!");
        return;
    }

    // Extract headers & fields
    let headers = [];
    let fields = [];

    table.querySelectorAll("thead th").forEach(th => {

        let field = th.dataset.field; // real key from table

        if (!field) return;     // skip # and Actions

        headers.push(th.innerText.trim());
        fields.push(field);
    });

    // Prepare rows using data-field mapping
    let body = data.map(row => {
        return fields.map(f => row[f] ?? "");
    });

    // Generate PDF
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF();

    doc.text("Exported Data", 14, 15);

    doc.autoTable({
        head: [headers],
        body: body,
        startY: 20,
        theme: "grid"
    });

    doc.save("export.pdf");
});

