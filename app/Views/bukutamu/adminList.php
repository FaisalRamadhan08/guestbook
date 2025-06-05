<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesan Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #eef1f5;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 30px;
        }

        .actions {
            margin-bottom: 25px;
            overflow: hidden;
        }

        .export-button {
            float: right;
            padding: 10px 18px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .export-button:hover {
            background-color: #218838;
        }

        .filter-form {
            margin-bottom: 25px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            background-color: #fcfcfc;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-form div {
            flex-grow: 1;
            min-width: 150px;
        }

        .filter-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .filter-form input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #444;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-data {
            text-align: center;
            color: #777;
            margin-top: 30px;
            padding: 20px;
            background-color: #fcfcfc;
            border-radius: 8px;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (session()->getFlashdata('success')) : ?>
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
        <h2>Daftar Pesan Buku Tamu (Admin)</h2>

        <?php
        $exportUrl = url_to('BukuTamu::exportCsv');
        if (!empty($startDate) && !empty($endDate)) {
            $exportUrl .= '?start_date=' . esc($startDate) . '&end_date=' . esc($endDate);
        }
        ?>

        <div class="actions">
            <a href="<?= url_to('BukuTamu::unsetAdmin') ?>" class="unset-admin-button">Nonaktifkan Mode Admin</a>
        </div>

        <div class="actions">
            <a href="<?= $exportUrl ?>" class="export-button">Export ke CSV</a>
        </div>


        <div class="filter-form">
            <?= form_open(url_to('BukuTamu::admin'), ['method' => 'get']) ?>
            <div>
                <label for="start_date">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="<?= esc($startDate) ?>">
            </div>
            <div>
                <label for="end_date">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="<?= esc($endDate) ?>">
            </div>
            <div>
                <button type="submit">Filter</button>
            </div>
            <?= form_close() ?>
        </div>

        <?php if (!empty($guests)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Pesan</th>
                        <th>Tanggal Submit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guests as $guest): ?>
                        <tr>
                            <td><?= esc($guest['id']) ?></td>
                            <td><?= esc($guest['nama']) ?></td>
                            <td><?= esc($guest['email']) ?></td>
                            <td><?= nl2br(esc($guest['pesan'])) ?></td>
                            <td><?= esc(date('d-m-Y H:i:s', strtotime($guest['created_at']))) ?></td>
                            <td>
                                <!-- Tombol Edit -->
                                <a href="<?= url_to('BukuTamu::edit', $guest['id']) ?>"
                                    style="background-color:#ffc107; color:white; padding:6px 12px; border-radius:5px; text-decoration:none; margin-right:5px;">
                                    Edit
                                </a>

                                <!-- Tombol Delete -->
                                <form action="<?= url_to('BukuTamu::delete', $guest['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                        style="background-color:#dc3545; color:white; padding:6px 12px; border:none; border-radius:5px; cursor:pointer;">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Belum ada pesan buku tamu.</p>
        <?php endif; ?>
    </div>
</body>

</html>