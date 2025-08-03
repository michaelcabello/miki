@php
    $oldPermissions = collect(old('permissions', []));
@endphp

@foreach ($permissions as $module => $modulePermissions)
    <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
            {{ ucfirst($module) }}
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-2 gap-4">
            @foreach ($modulePermissions as $permission)
                <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-200">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        class="mt-1.5 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        {{ $oldPermissions->contains($permission->name) ? 'checked' : '' }}>
                    <span>{{ $permission->display_name ?? $permission->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endforeach




{{-- @foreach ($permissions as $module => $modulePermissions)
    <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
            {{ ucfirst($module) }}
        </h4>


        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-2 gap-4">
            @foreach ($modulePermissions as $permission)
                <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-200">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        class="mt-1.5 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        {{ isset($model) && $model->permissions->contains($permission->id) ? 'checked' : '' }}>
                    <span>{{ $permission->display_name ?? $permission->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endforeach --}}


{{-- @foreach ($permissions as $module => $modulePermissions)
    <div class="mb-6">
        <h4 class="text-base font-semibold text-gray-800 dark:text-white border-b pb-1 mb-3">
            {{ ucfirst($module) }}
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4">
            @foreach ($modulePermissions as $permission)
                <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-200">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        class="mt-1.5 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        {{ isset($model) && $model->permissions->contains($permission->id) ? 'checked' : '' }}>
                    <span>{{ $permission->display_name ?? $permission->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
@endforeach --}}


