<?php
session_start();

// Inisialisasi data tugas awal di session (hanya sekali)
function initTasks(): void
{
    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [
            ["id" => 1, "title" => "Belajar PHP", "status" => "belum", "date" => date('Y-m-d')],
            ["id" => 2, "title" => "Kerjakan tugas UX", "status" => "selesai", "date" => date('Y-m-d')],
        ];
    }
}

// Buat id baru yang unik untuk tugas
function generateNewId(array $tasks): int
{
    if (empty($tasks)) {
        return 1;
    }
    $ids = array_column($tasks, 'id');
    return max($ids) + 1;
}

// Tambahkan satu tugas baru ke daftar (dengan tanggal)
function tambahTugas(string $title, string $date = ''): void
{
    $title = trim($title);
    if ($title === '') {
        return;
    }

    // Validasi format tanggal (YYYY-MM-DD); kalau kosong/tidak valid, pakai tanggal hari ini
    $d = \DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        $date = date('Y-m-d');
    }

    $newTask = [
        "id"     => generateNewId($_SESSION['tasks']),
        "title"  => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        "status" => "belum",
        "date"   => $date,
    ];
    $_SESSION['tasks'][] = $newTask;
}

// Ubah status tugas: belum <-> selesai
function toggleStatus(int $id): void
{
    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['status'] = ($task['status'] === 'selesai') ? 'belum' : 'selesai';
            break;
        }
    }
    unset($task);
}

// Ubah judul/teks tugas berdasarkan id
function editTugas(int $id, string $newTitle): void
{
    $newTitle = trim($newTitle);
    if ($newTitle === '') {
        return;
    }
    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] === $id) {
            $task['title'] = htmlspecialchars($newTitle, ENT_QUOTES, 'UTF-8');
            break;
        }
    }
    unset($task);
}

// Hapus satu tugas berdasarkan id
function hapusTugas(int $id): void
{
    $_SESSION['tasks'] = array_values(
        array_filter($_SESSION['tasks'], function ($task) use ($id) {
            return $task['id'] !== $id;
        })
    );
}

// Hapus beberapa tugas sekaligus (fitur select all)
function hapusBeberapaTugas(array $ids): void
{
    $ids = array_map('intval', $ids);

    $_SESSION['tasks'] = array_values(
        array_filter($_SESSION['tasks'], function ($task) use ($ids) {
            return !in_array($task['id'], $ids, true);
        })
    );
}

// Cetak semua baris tugas ke dalam tabel HTML
function tampilkanDaftar(array $tasks): void
{
    if (empty($tasks)) {
        echo '<tr><td colspan="6" class="text-center text-muted">Belum ada tugas.</td></tr>';
        return;
    }

    foreach ($tasks as $task) {
        $isDone   = $task['status'] === 'selesai';
        $checked  = $isDone ? 'checked' : '';
        $textStyle = $isDone ? 'text-decoration: line-through; color: #888;' : '';
        $badge    = $isDone
            ? '<span class="badge bg-success">Selesai</span>'
            : '<span class="badge bg-warning text-dark">Belum</span>';
        $id = (int)$task['id'];
        $tanggal = $task['date'] ?? '-';

        echo '<tr>';
        echo '<td style="width:5%">
                <input type="checkbox" class="form-check-input row-checkbox"
                       name="selected[]" value="' . $id . '" form="bulkDeleteForm">
              </td>';
        echo '<td style="width:5%">
                <form method="post" class="m-0">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="' . $id . '">
                    <input type="checkbox" class="form-check-input" onchange="this.form.submit()" ' . $checked . '>
                </form>
              </td>';
        echo '<td style="' . $textStyle . '">
                <span id="title-view-' . $id . '">' . htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') . '</span>
                <form method="post" class="d-none d-flex gap-1" id="title-edit-' . $id . '">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="' . $id . '">
                    <input type="text" name="title" class="form-control form-control-sm"
                           value="' . htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') . '" required>
                    <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleEdit(' . $id . ')">Batal</button>
                </form>
              </td>';
        echo '<td style="width:15%">' . $badge . '</td>';
        echo '<td style="width:12%">' . htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td style="width:15%">
                <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleEdit(' . $id . ')">Edit</button>
                <form method="post" class="m-0" onsubmit="return confirm(\'Hapus tugas ini?\');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="' . $id . '">
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
                </div>
              </td>';
        echo '</tr>';
    }
}