{{-- PRUEBA: Este es el paginador personalizado --}}

@if ($paginator->hasPages())
    <nav>
        <ul class="pagination flex gap-2 text-sm text-gray-700">
            {{-- Enlace a la página anterior --}}
            @if ($paginator->onFirstPage())
                <li class="disabled px-3 py-2 rounded-md bg-gray-200 text-gray-500 cursor-not-allowed" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition" aria-label="{{ __('pagination.previous') }}">
                        &laquo;
                    </a>
                </li>
            @endif

            {{-- Elementos de paginación --}}
            @foreach ($elements as $element)
                {{-- Separador de "Tres Puntos" --}}
                @if (is_string($element))
                    <li class="px-3 py-2 text-gray-500">{{ $element }}</li>
                @endif

                {{-- Array de enlaces --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="px-3 py-2 rounded-md bg-blue-500 text-white font-semibold">{{ $page }}</li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="px-3 py-2 rounded-md bg-gray-100 hover:bg-gray-200 transition">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Enlace a la página siguiente --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition" aria-label="{{ __('pagination.next') }}">
                        &raquo;
                    </a>
                </li>
            @else
                <li class="disabled px-3 py-2 rounded-md bg-gray-200 text-gray-500 cursor-not-allowed" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
