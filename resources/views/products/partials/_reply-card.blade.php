@php
    $currentUser = auth()->user();
    $isOwner     = $currentUser && $reply->user_id === $currentUser->id;
    $isAdmin     = $currentUser && $currentUser->hasRole('admin');
@endphp

<div class="pdr-reply" id="reply-{{ $reply->id }}">
    <div class="pdr-reply-line"></div>

    <div class="pdr-reply-body">
        <div class="pdr-reply-head">
            <div class="pdr-reply-avatar {{ $reply->is_admin ? 'admin' : '' }}">
                {{ $reply->is_admin ? '🏪' : strtoupper(substr($reply->user->name, 0, 1)) }}
            </div>

            <div class="pdr-reply-info">
                <div class="pdr-reply-name">
                    {{ $reply->user->name }}
                    @if ($reply->is_admin)
                        <span class="pdr-badge admin">Store Owner</span>
                    @endif
                    @if ($isOwner)
                        <span class="pdr-badge you">You</span>
                    @endif
                </div>
                <div class="pdr-reply-meta">
                    <span>{{ $reply->created_at->diffForHumans() }}</span>
                    @if ($reply->isEdited())
                        <span>·</span>
                        <span class="pdr-edited">edited</span>
                    @endif
                </div>
            </div>

            @if ($isOwner || $isAdmin)
                <form method="POST" action="{{ route('replies.destroy', $reply) }}"
                      onsubmit="return confirm('Delete this reply?');" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="pdr-icon-btn danger sm" title="Delete">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            @endif
        </div>

        <div class="pdr-reply-comment">{{ $reply->comment }}</div>
    </div>
</div>