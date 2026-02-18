<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Partners</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>

<h2>Reporte de Partners</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Cliente</th>
            <th>Proveedor</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($partners as $partner)
            <tr>
                <td>{{ $partner->id }}</td>
                <td>{{ $partner->name }}</td>
                <td>{{ $partner->email }}</td>
                <td>{{ $partner->phone }}</td>
                <td>{{ $partner->is_customer ? 'Sí' : 'No' }}</td>
                <td>{{ $partner->is_supplier ? 'Sí' : 'No' }}</td>
                <td>{{ $partner->status ? 'Activo' : 'Inactivo' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
