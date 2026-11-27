@if (session('status'))
    <div class="mb-5 rounded-[10px] border border-[#B8E0C8] bg-[#E3F4EA] px-3 py-2 text-sm text-[#1E7B45]" role="status">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-5 rounded-[10px] border border-[#F3C1C1] bg-[#FDE7E7] px-3 py-2 text-sm text-[#B3261E]" role="alert">
        {{ session('error') }}
    </div>
@endif
