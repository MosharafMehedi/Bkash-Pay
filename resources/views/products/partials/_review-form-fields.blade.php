@php
    $isEdit = $review !== null;
    $rating = old('rating', $review->rating ?? 5);
    $title  = old('title', $review->title ?? '');
    $comment = old('comment', $review->comment ?? '');
@endphp

{{-- Star rating selector --}}
<div class="pdr-field">
    <label class="pdr-label">Your Rating <span class="req">*</span></label>
    <div class="pdr-star-input" id="starInput">
        @for ($i = 1; $i <= 5; $i++)
            <button type="button"
                    class="pdr-star {{ $i <= $rating ? 'active' : '' }}"
                    data-value="{{ $i }}">★</button>
        @endfor
        <input type="hidden" name="rating" id="ratingInput" value="{{ $rating }}" required>
    </div>
    @error('rating') <div class="pdr-error">{{ $message }}</div> @enderror
</div>

{{-- Title --}}
<div class="pdr-field">
    <label class="pdr-label">Title <span style="color:var(--text-mu);font-weight:400;">(optional)</span></label>
    <input type="text" name="title" value="{{ $title }}"
           maxlength="150" placeholder="Summary of your review">
    @error('title') <div class="pdr-error">{{ $message }}</div> @enderror
</div>

{{-- Comment --}}
<div class="pdr-field">
    <label class="pdr-label">Your Review <span class="req">*</span></label>
    <textarea name="comment" rows="4" minlength="10" maxlength="1000"
              placeholder="What did you like or dislike? (min 10 characters)">{{ $comment }}</textarea>
    @error('comment') <div class="pdr-error">{{ $message }}</div> @enderror
</div>

<div class="pdr-form-actions">
    <button type="submit" class="pdr-submit">{{ $submitText }}</button>
    @if (! empty($cancelEdit))
        <button type="button" class="pdr-cancel" onclick="
            document.getElementById('editReviewCard').style.display='none';
            document.getElementById('reviewedNotice').style.display='';
        ">Cancel</button>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const starInput  = document.getElementById('starInput');
    const ratingInput = document.getElementById('ratingInput');

    if (! starInput) return;

    starInput.querySelectorAll('.pdr-star').forEach((btn) => {
        btn.addEventListener('click', () => {
            const value = parseInt(btn.dataset.value);
            ratingInput.value = value;

            starInput.querySelectorAll('.pdr-star').forEach((s) => {
                s.classList.toggle('active', parseInt(s.dataset.value) <= value);
            });
        });

        btn.addEventListener('mouseenter', () => {
            const hoverValue = parseInt(btn.dataset.value);
            starInput.querySelectorAll('.pdr-star').forEach((s) => {
                s.classList.toggle('hover', parseInt(s.dataset.value) <= hoverValue);
            });
        });
    });

    starInput.addEventListener('mouseleave', () => {
        starInput.querySelectorAll('.pdr-star').forEach((s) => s.classList.remove('hover'));
    });
})();
</script>
@endpush