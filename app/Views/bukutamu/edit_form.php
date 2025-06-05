<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #eef1f5;
            color: #333;
        }

        .container {
            max-width: 600px;
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .success {
            color: #28a745;
            font-size: 0.9em;
            margin-top: 5px;
            text-align: center;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
        }

        .button {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.2s ease-in-out;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Tamu</h2>

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

        <?php
        // Ambil semua error validasi dari flashdata
        $validationErrors = session()->getFlashdata('errors');
        ?>

        <?php if ($validationErrors) : ?>
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($validationErrors as $field => $error) : ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= url_to('BukuTamu::update', $tamu['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $tamu['id'] ?>" />
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="<?= esc(old('nama', $tamu['nama'])) ?>">
                <?php if (isset($validationErrors['nama'])): ?>
                    <div class="error"><?= $validationErrors['nama'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= esc(old('email', $tamu['email'])) ?>">
                <?php if (isset($validationErrors['email'])): ?>
                    <div class="error"><?= $validationErrors['email'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="pesan">Pesan:</label>
                <textarea id="pesan" name="pesan"><?= esc(old('pesan', $tamu['pesan'])) ?></textarea>
                <?php if (isset($validationErrors['pesan'])): ?>
                    <div class="error"><?= $validationErrors['pesan'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <button type="submit" class="button">Update Pesan</button>
            </div>
        </form>
        <p style="text-align:center;">
            <a href="<?= base_url('/admin') ?>">Kembali ke daftar</a>
        </p>


    </div>
</body>

</html>