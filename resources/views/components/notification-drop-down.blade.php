@php
    $notifications = auth()->user()
        ->notifications()
        ->latest()
        ->take(10)
        ->get();
@endphp
<div
    id="notificationDropDown"
    data-read-url="{{ route('notifications.read') }}"
    data-has-unread="{{ $notifications->contains(fn ($notification) => ! $notification->read) ? '1' : '0' }}"
    class="hidden absolute right-10 top-16 mt-2 w-100 rounded-lg bg-gray-800 shadow-lg ring-1 ring-black/10 z-50 p-5 text-gray-500"
>
    @if($notifications->isNotEmpty())
        @foreach($notifications as $notification)
            <div class="mb-6 rounded-lg border {{ $notification->read ? 'border-gray-700 bg-gray-800/60' : 'border-blue-500/40 bg-gray-900/70' }} p-4">
                <div class="mb-2 flex items-start justify-between gap-3 {{ $notification->read ? '' : 'text-white' }}">
                    <div>
                        <h1 class="text-lg text-blue-400">{{$notification->title}}</h1>
                        <p class="text-sm">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if (! $notification->read)
                        <button
                            type="button"
                            class="notification-read-button inline-flex items-center justify-center rounded-lg bg-blue-700 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-600"
                            data-read-url="{{ route('notifications.update', $notification) }}"
                        >
                            Read
                        </button>
                    @endif
                </div>
                <p class="text-sm {{$notification->read ? 'text-gray-400' : 'text-gray-200'}}">{{$notification->body}}</p>
            </div>
        @endforeach
    @else
        No Notifications
    @endif
</div>

<script>
    (() => {
        const notificationDropDown = document.getElementById('notificationDropDown');
        const button = document.getElementById('notificationButton');
        const unreadIndicator = document.getElementById('notificationUnreadIndicator');
        const readButtons = () => Array.from(notificationDropDown.querySelectorAll('.notification-read-button'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!notificationDropDown || !button) {
            return;
        }

        let hasUnread = notificationDropDown.dataset.hasUnread === '1';
        const syncUnreadState = () => {
            const hasAnyUnread = readButtons().length > 0;

            if (!hasAnyUnread) {
                hideUnreadIndicator();
            }
        };

        const hideUnreadIndicator = () => {
            unreadIndicator?.classList.add('hidden');
            notificationDropDown.dataset.hasUnread = '0';
            hasUnread = false;
        };

        const markSingleAsRead = async (readButton) => {
            if (!csrfToken || !readButton?.dataset.readUrl) {
                return;
            }

            readButton.disabled = true;

            try {
                const response = await fetch(readButton.dataset.readUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    readButton.disabled = false;
                    return;
                }

                const card = readButton.closest('.rounded-lg');
                const body = card?.querySelector('p.text-sm');
                const header = card?.querySelector('.flex');

                readButton.remove();
                card?.classList.remove('border-blue-500/40', 'bg-gray-900/70');
                card?.classList.add('border-gray-700', 'bg-gray-800/60');
                body?.classList.remove('text-gray-200');
                body?.classList.add('text-gray-400');
                header?.classList.remove('text-white');

                syncUnreadState();
            } catch (error) {
                readButton.disabled = false;
            }
        };

        notificationDropDown.addEventListener('click', (event) => {
            const readButton = event.target.closest('.notification-read-button');

            if (!readButton) {
                return;
            }

            markSingleAsRead(readButton);
        });

        button.addEventListener('click', () => {
            notificationDropDown.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!button.contains(event.target) && !notificationDropDown.contains(event.target)) {
                notificationDropDown.classList.add('hidden');
            }
        });
    })();
</script>
