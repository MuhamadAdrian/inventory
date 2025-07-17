<div class="dropdown">
    <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
        <i class="bi bi-bell fs-4"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notifCount">
            {{ auth()->user()->notifications()->wherePivotNull('read_at')->count() }}
        </span>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2" style="width: 300px;max-height: 300px;font-size: 12px; overflow-y: scroll" id="notifList">
        @forelse (auth()->user()->notifications()->latest()->take(10)->get() as $n)
            <li class="dropdown-item text-wrap @if(!$n->pivot->read_at) bg-light @endif" data-id="{{ $n->id }}" style="cursor: pointer;">
                <div class="fw-bold">{{ strtoupper(str_replace('_', ' ', $n->type)) }}</div>
                <div>{{ $n->message }}</div>
                <small class="text-muted">{{ $n->created_at->format('d M Y H:i') }}</small>
            </li>
        @empty
            <li class="text-center text-muted">No notifications</li>
        @endforelse
    </ul>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const notifList = document.getElementById('notifList');
    const notifCount = document.getElementById('notifCount');

    notifList.addEventListener('click', function (e) {
        const item = e.target.closest('li[data-id]');
        if (!item) return;

        const id = item.getAttribute('data-id');

        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(() => {
            item.classList.remove('bg-light');
            notifCount.textContent = Math.max(0, parseInt(notifCount.textContent) - 1);
        });
    });

    // Optional: auto-refresh every 30s
    setInterval(() => {
        location.reload(); // or use AJAX to reload just the notification list
    }, 30000);
});
</script>
@endpush