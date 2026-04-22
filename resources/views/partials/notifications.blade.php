@php
    $messages = collect([
        ['type' => 'success', 'message' => session('success')],
        ['type' => 'danger', 'message' => session('error')],
        ['type' => 'warning', 'message' => session('warning')],
        ['type' => 'info', 'message' => session('info')],
    ])->filter(fn ($item) => filled($item['message']))->values();

    if (isset($errors) && $errors->any()) {
        $messages = $messages->prepend([
            'type' => 'danger',
            'message' => $errors->first(),
        ]);
    }
@endphp

@if($messages->isNotEmpty())
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
        @foreach($messages as $item)
            <div class="toast align-items-center text-bg-{{ $item['type'] }} border-0 shadow mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ $item['message'] }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endforeach
    </div>

    @push('scripts')
        <script>
            (() => {
                const nodes = document.querySelectorAll('.toast-container .toast')
                nodes.forEach((el) => {
                    const t = bootstrap.Toast.getOrCreateInstance(el)
                    t.show()
                })
            })()
        </script>
    @endpush
@endif
