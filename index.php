<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validasi & Tabel Data</title>
    <script src="https://cdn.jsdelivr.net/npm/just-validate@3.5.0/dist/just-validate.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Styling sama seperti sebelumnya */
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f0f0f0; }
        form { margin-bottom: 20px; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input { width: 100%; padding: 10px; margin-top: 8px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; background-color: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #bf00ff; color: white; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; gap: 5px; }
        .pagination button { padding: 10px; border: none; background-color: #bf00ff; color: white; cursor: pointer; border-radius: 5px; }
        .pagination button.disabled { background-color: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>
    <form id="dataForm">
        <div>
            <input type="text" name="nik" class="nik" placeholder="Masukkan NIK">
            <p class="error nik--error"></p>
        </div>
        <div>
            <input type="text" name="name" class="name" placeholder="Masukkan Nama">
            <p class="error nama--error"></p>
        </div>
        <button type="submit">Simpan</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="pagination"></div>

    <script>
    let currentPage = 1;
    const itemsPerPage = 5;

    // Fungsi untuk memuat data dari server
    function loadData(page = 1) {
        axios.get(`get-students.php?page=${page}&limit=${itemsPerPage}`)
            .then(response => {
                const tableBody = document.querySelector('table tbody');
                const pagination = document.querySelector('.pagination');
                const data = response.data;

                tableBody.innerHTML = '';
                pagination.innerHTML = '';

                if (data.students.length > 0) {
                    data.students.forEach((student, index) => {
                        tableBody.innerHTML += `
                            <tr>
                                <td>${(page - 1) * itemsPerPage + index + 1}</td>
                                <td>${student.nik}</td>
                                <td>${student.nama}</td>
                            </tr>
                        `;
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="3">No data available</td></tr>';
                }

                // Pagination controls
                if (data.totalPages > 1) {
                    for (let i = 1; i <= data.totalPages; i++) {
                        pagination.innerHTML += `
                            <button class="${i === page ? 'disabled' : ''}" ${i === page ? 'disabled' : ''} onclick="loadData(${i})">${i}</button>
                        `;
                    }
                }
            })
            .catch(error => {
                alert("Error loading data.");
                console.error(error);
            });
    }

    loadData();

    // Validasi form dengan JustValidate
    const validation = new JustValidate('#dataForm');
    validation
    .addField('.nik', [
        { rule: 'required', errorMessage: 'NIK tidak boleh kosong' },
        { rule: 'number', errorMessage: 'NIK harus berupa angka' },
    ])
    .addField('.name', [
        { rule: 'required', errorMessage: 'Nama tidak boleh kosong' },
        { rule: 'minLength', value: 3, errorMessage: 'Nama minimal 3 karakter' },
    ])
    .onSuccess(event => {
        event.preventDefault();
        const nik = document.querySelector('.nik').value;
        const name = document.querySelector('.name').value;

        axios.post('save-students.php', { nik, name })
            .then(response => {
                const res = response.data;
                if (res.status === true && res.student) {
                    const table = document.querySelector('table tbody');
                    const newRow = `
                        <tr>
                            <td>${table.rows.length + 1}</td>
                            <td>${res.student.nik}</td>
                            <td>${res.student.nama}</td>
                        </tr>
                    `;
                    table.innerHTML += newRow; // Tambahkan data baru ke tabel
                    event.target.reset(); // Reset form setelah berhasil
                } else {
                    alert(res.error || "An unexpected error occurred");
                }
            })
            .catch(error => {
                alert("Failed to save data.");
                console.error(error);
            });
    });
</script>
</body>
</html>

