document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.favorite-btn');
    if (!btn) return;

    e.preventDefault();

    const movieId = btn.dataset.movieId;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // optimistic UI
    btn.classList.toggle('is-active');

    try {
        const res = await fetch(`/favorites/movie/${movieId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (!res.ok) {
            // rollback
            btn.classList.toggle('is-active');
            alert(data.message || 'Ошибка');
            return;
        }

        // 🔢 обновляем счётчик
        const counter = document.getElementById('favoritesCounter');
        if (counter && typeof data.count !== 'undefined') {
            counter.textContent = data.count;
            counter.classList.add('animate-pulse');
            setTimeout(() => counter.classList.remove('animate-pulse'), 500);
        }

        // если удалили из избранного на странице /favorites
        if (data.status === 'removed') {
            const card = btn.closest('.favorite-card');
            if (card) {
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 250);
            }
        }

    } catch (err) {
        console.error(err);
        btn.classList.toggle('is-active'); // rollback
        alert('Ошибка соединения');
    }
});
