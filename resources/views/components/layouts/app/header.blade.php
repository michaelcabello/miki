<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    @stack('styles')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Home') }}
            </flux:navbar.item>
        </flux:navbar>



        <flux:spacer />

        <flux:navbar class="mr-1.5 space-x-0.5 py-0!">
            <flux:tooltip :content="__('Search')" position="bottom">
                <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#"
                    :label="__('Search')" />
            </flux:tooltip>
            <flux:tooltip :content="__('Repository')" position="bottom">
                <flux:navbar.item class="h-10 max-lg:hidden [&>div>svg]:size-5" icon="folder-git-2"
                    href="https://github.com/laravel/livewire-starter-kit" target="_blank" :label="__('Repository')" />
            </flux:tooltip>
            <flux:tooltip :content="__('Documentation')" position="bottom">
                <flux:navbar.item class="h-10 max-lg:hidden [&>div>svg]:size-5" icon="book-open-text"
                    href="https://laravel.com/docs/starter-kits" target="_blank" label="Documentation" />
            </flux:tooltip>
        </flux:navbar>

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky
        class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ml-1 flex items-center space-x-2" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')">
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    {{ $slot }}

    @fluxScripts

    @stack('modals')
    @stack('scripts')
    {{-- esto se usa para laravel tradicional --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastData = @json(session('swal'), JSON_PRETTY_PRINT);

            if (toastData && toastData.title) {
                Swal.fire({
                    title: toastData.title ?? 'Listo',
                    text: toastData.text ?? '',
                    icon: toastData.icon ?? 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                });
            }
        });
    </script>
    {{-- fin de esto se usa para laravel tradicional --}}


    <script>
        document.addEventListener('livewire:navigated', () => {

            //parametro para edicion o actualizacion
            Livewire.on('show-swalindex', ({
                title,
                text,
                icon
            }) => {
                Swal.fire({
                    title: title ?? 'TICOM',
                    text: text ?? '',
                    icon: icon ?? 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            });

            //parametro para edicion o actualizacion en la parte superior derecha
            Livewire.on('notify', ({
                type,
                message
            }) => {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            });


            //inicia eliminar
            /* window.confirmDeletesimple = function(id, eventName = 'deleteSingle', message =
                'Este registro será eliminado.') { */
            // Confirmación simple global para eliminar cualquier recurso
            window.confirmDeletesimple = function(id, name, eventName = 'deleteSingle', message =
                'Este registro será eliminado.') {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //console.log('Enviando evento:', eventName, 'con id:', id);
                        Livewire.dispatch(eventName, [id, name]);
                    }
                });
            };

            // Mensaje de éxito al eliminar (global)
            Livewire.on('itemDeleted', ({
                title,
                text,
                icon
            }) => {
                Swal.fire({
                    title: title ?? '¡Eliminado!',
                    text: text ?? 'El registro fue eliminado correctamente.',
                    icon: icon ?? 'success',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#3085d6',
                });
            });

            //termina eliminar




        });
    </script>

</body>

</html>
