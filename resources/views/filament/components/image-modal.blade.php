<div x-data="{ 
    open: false,
    imageUrl: '',
    init() {
        window.addEventListener('open-modal', (e) => {
            this.imageUrl = e.detail.image;
            this.open = true;
        });
    }
}" x-show="open" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    @click.self="open = false">
    <div class="relative max-w-[90vw] max-h-[90vh]">
        <button @click="open = false" class="absolute top-2 right-2 text-white bg-black/50 rounded-full p-2 hover:bg-black/75">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img :src="imageUrl" class="max-w-full max-h-[90vh] object-contain" />
    </div>
</div>