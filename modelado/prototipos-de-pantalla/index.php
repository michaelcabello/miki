<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>ERP - POS</title>
</head>
<body class="bg-slate-50 text-slate-800">
  <div class="min-h-screen">
    <!-- Topbar -->
    <?php include 'header.php'; ?>

    <!-- Body -->
    <div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-12 gap-6">
      <!-- Sidebar -->
        <?php include 'menu.php'; ?>

      <!-- Main -->
      <main class="col-span-12 md:col-span-9 lg:col-span-10">
        <!-- Aquí pegas cada pantalla -->

        <?php include 'pantallas/almacenes.html'; ?>


      </main>
    </div>
  </div>
</body>
</html>

