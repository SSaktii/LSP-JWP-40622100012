<?php
require_once __DIR__ . '/functions.php';

initTasks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            tambahTugas($_POST['title'] ?? '');
            break;

        case 'toggle':
            toggleStatus((int)($_POST['id'] ?? 0));
            break;

        case 'delete':
            hapusTugas((int)($_POST['id'] ?? 0));
            break;

        case 'delete_selected':
            hapusBeberapaTugas($_POST['selected'] ?? []);
            break;
    }

    header('Location: index.php');
    exit;
}

$tasks = $_SESSION['tasks'];
$totalTugas   = count($tasks);
$totalSelesai = count(array_filter($tasks, fn($t) => $t['status'] === 'selesai'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi To-Do List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fa; }
        .app-card { max-width: 700px; margin: 40px auto; }
        header.app-header { background: #72718e; color: #fff; padding: 1.25rem 1.5rem; border-radius: .5rem .5rem 0 0; }
    </style>
</head>
<body>

<div class="app-card card shadow-sm">
    <header class="app-header">
        <h1 class="h4 mb-0"> Aplikasi To-Do List</h1>
        <small><?= $totalSelesai ?> dari <?= $totalTugas ?> tugas selesai</small>
    </header>

    <div class="card-body">

        <form method="post" class="row g-2 mb-4">
            <input type="hidden" name="action" value="add">
            <div class="col-9">
                <input type="text" name="title" class="form-control" placeholder="Tulis tugas baru..." required>
            </div>
            <div class="col-3 d-grid">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>

        <table class="table align-middle">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCheckbox" title="Pilih semua"></th>
                    <th></th>
                    <th>Tugas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php tampilkanDaftar($tasks); ?>
            </tbody>
        </table>

        <form id="bulkDeleteForm" method="post"
              onsubmit="return confirm('Hapus semua tugas yang dipilih?');">
            <input type="hidden" name="action" value="delete_selected">
            <button type="submit" class="btn btn-danger btn-sm mt-2">
                 Hapus Tugas yang Dipilih
            </button>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('selectAllCheckbox').addEventListener('change', function () {
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
</body>
</html>