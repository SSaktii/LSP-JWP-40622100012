<?php
session_start();

function initTasks(): void
{
    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [
            ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
            ["id" => 2, "title" => "Kerjakan tugas UX", "status" => "selesai"],
        ];
    }
}

function generateNewId(array $tasks): int
{
    if (empty($tasks)) {
        return 1;
    }
    $ids = array_column($tasks, 'id');
    return max($ids) + 1;
}

function tambahTugas(string $title): void
{
    $title = trim($title);
    if ($title === '') {
        return;
    }
    $newTask = [
        "id"     => generateNewId($_SESSION['tasks']),
        "title"  => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        "status" => "belum",
    ];
    $_SESSION['tasks'][] = $newTask;
}

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

function hapusTugas(int $id): void
{
    $_SESSION['tasks'] = array_values(
        array_filter($_SESSION['tasks'], function ($task) use ($id) {
            return $task['id'] !== $id;
        })
    );
}

function hapusBeberapaTugas(array $ids): void
{
    $ids = array_map('intval', $ids);

    $_SESSION['tasks'] = array_values(
        array_filter($_SESSION['tasks'], function ($task) use ($ids) {
            return !in_array($task['id'], $ids, true);
        })
    );
}

function tampilkanDaftar(array $tasks): void
{
    if (empty($tasks)) {
        echo '<tr><td colspan="5" class="text-center text-muted">Belum ada tugas.</td></tr>';
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
        echo '<td style="' . $textStyle . '">' . htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td style="width:15%">' . $badge . '</td>';
        echo '<td style="width:10%">
                <form method="post" class="m-0" onsubmit="return confirm(\'Hapus tugas ini?\');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="' . $id . '">
                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                </form>
              </td>';
        echo '</tr>';
    }
}