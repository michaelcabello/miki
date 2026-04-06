<style>
    @if ($settings->paper_size == '80mm')
        @page {
            margin: 5px;
        }

        /* Márgenes mínimos para ticketera */
        body {
            margin: 5px;
            font-family: 'Courier', sans-serif;
        }

        /* Fuente tipo ticketera */
    @else
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
        }
    @endif

    table {
        width: 100%;
        border-collapse: collapse;
    }


    /* 🚀 Fallback de color si la base de datos falla */
    @php $primary =$settings->primary_color ?? '#4f46e5';
    @endphp

    @page {
        margin: 0.8cm;
    }

    body {
        font-family: sans-serif;
        font-size: 11px;
        color: #333;
    }

    .primary-text {
        color: {{ $primary }};
    }

    .primary-bg {
        background-color: {{ $primary }};
        color: white;
    }

    .border-primary {
        border: 2px solid {{ $primary }};
    }

    .header-box {
        border: 2px solid {{ $primary }};
        padding: 15px;
        border-radius: 10px;
        text-align: center;
    }

    /* resources/views/admin/pdf/layouts/styles.blade.php */
    .company-logo {
        display: block;
        width: 160px;
        height: auto;
        max-height: 85px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 8px;
        border-bottom: 2px solid #eee;
    }

    td {
        padding: 8px;
        border-bottom: 1px solid #f9f9f9;
    }
</style>
