@if ($paginator->hasPages())
    <nav style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:8px">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="display:inline-flex;align-items:center;padding:8px 12px;background:#f3f4f6;color:#9ca3af;border-radius:6px;border:1px solid #e5e7eb;cursor:not-allowed">
                    <svg style="width:16px;height:16px;margin-right:4px" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   style="display:inline-flex;align-items:center;padding:8px 12px;background:#fff;color:#374151;border-radius:6px;border:1px solid #d1d5db;text-decoration:none;transition:all 0.2s"
                   onmouseover="this.style.backgroundColor='#f9fafb';this.style.borderColor='#9ca3af'"
                   onmouseout="this.style.backgroundColor='#fff';this.style.borderColor='#d1d5db'">
                    <svg style="width:16px;height:16px;margin-right:4px" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            <div style="display:flex;align-items:center;gap:4px">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span style="padding:8px 4px;color:#9ca3af">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span style="display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;background:#3b82f6;color:white;border-radius:6px;border:1px solid #3b82f6;font-weight:600;min-width:40px">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" 
                                   style="display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;background:#fff;color:#374151;border-radius:6px;border:1px solid #d1d5db;text-decoration:none;min-width:40px;transition:all 0.2s"
                                   onmouseover="this.style.backgroundColor='#f9fafb';this.style.borderColor='#9ca3af'"
                                   onmouseout="this.style.backgroundColor='#fff';this.style.borderColor='#d1d5db'">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   style="display:inline-flex;align-items:center;padding:8px 12px;background:#fff;color:#374151;border-radius:6px;border:1px solid #d1d5db;text-decoration:none;transition:all 0.2s"
                   onmouseover="this.style.backgroundColor='#f9fafb';this.style.borderColor='#9ca3af'"
                   onmouseout="this.style.backgroundColor='#fff';this.style.borderColor='#d1d5db'">
                    Next
                    <svg style="width:16px;height:16px;margin-left:4px" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            @else
                <span style="display:inline-flex;align-items:center;padding:8px 12px;background:#f3f4f6;color:#9ca3af;border-radius:6px;border:1px solid #e5e7eb;cursor:not-allowed">
                    Next
                    <svg style="width:16px;height:16px;margin-left:4px" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </span>
            @endif
        </div>

        {{-- Page Info --}}
        <div style="color:#6b7280;font-size:14px">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
    </nav>
@endif
