<x-layouts.app :title="__('Usuarios')">
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Importar Usuarios desde Excel</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="file" class="block mb-2 text-gray-700 dark:text-gray-200">Archivo Excel (.xlsx o
                    .csv)</label>
                <input type="file" name="file" id="file" accept=".xlsx,.csv"
                    class="border border-gray-300 rounded p-2 w-full">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fa-solid fa-upload mr-2"></i> Importar
            </button>
        </form>
    </div>
</x-layouts.app>
