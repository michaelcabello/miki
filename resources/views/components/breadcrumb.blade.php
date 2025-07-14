<div>
    <nav class="flex px-4 py-2 text-sm text-gray-600 dark:text-gray-300" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            @foreach ($links as $label => $url)
                <li class="inline-flex items-center">
                    @if (!$loop->last)
                        <a href="{{ $url }}"
                            class="inline-flex items-center text-sm font-medium hover:text-blue-600 dark:hover:text-white">
                            @if ($loop->first)
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a1 1 0 00-.707.293l-7 7a1 1 0 101.414 1.414L4 10.414V17a1 1 0 001 1h3a1 1 0 001-1v-3h2v3a1 1 0 001 1h3a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7A1 1 0 0010 2z" />
                                </svg>
                            @endif
                            {{ $label }}
                        </a>
                    @else
                        <span class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $label }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

</div>
