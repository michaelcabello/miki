<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    {{-- 🚀 Inyectamos el CSS directamente en el head --}}
    @include('admin.pdf.layouts.styles')
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
