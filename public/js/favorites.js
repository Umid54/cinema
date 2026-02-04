document.addEventListener('DOMContentLoaded', () => {

    const counter = document.getElementById('favoritesCounter');

    function pulse() {
        counter.classList.add('scale-125');
        setTimeout(() => counter.classList.remove('scale-125'), 200);
    }

    async function toggleFavorite(movieId, btn) {
        try {
            const res = await fetch(`/favorites/movie/${movieId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                alert(data.message ?? 'Ошибка');
                return;
            }

            counter.textContent = data.count;
            pulse();

            // LIMIT INFO
            if (data.limit_reached) {
                counter.classList.add('bg-amber-500/30', 'border-amber-400/50');
            }

            btn.classList.toggle('text-rose-300', data.added);

        } catch (e) {
            console.error(e);
            alert('Ошибка соединения');
        }
    }

    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            toggleFavorite(btn.dataset.movieId, btn);
        });
    });

});
