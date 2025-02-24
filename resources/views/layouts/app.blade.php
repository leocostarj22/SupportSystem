<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

    const channel = pusher.subscribe('ticket-channel');
    channel.bind('new-message', function(data) {
        if (window.location.href.includes(data.ticket_id)) {
            Livewire.dispatch('refreshComponent');
        }
    });
</script>