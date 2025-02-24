<script>
    document.addEventListener('livewire:initialized', () => {
        const chatContainer = document.querySelector('.fi-ta-content');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
            
            // Auto-scroll on new messages
            const observer = new MutationObserver(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
            
            observer.observe(chatContainer, {
                childList: true,
                subtree: true
            });
        }
    });
</script>