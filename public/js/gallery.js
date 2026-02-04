(function () {

    let modal, image, closeBtn, nextBtn, prevBtn;
    let thumbs = [];
    let images = [];
    let currentIndex = 0;
    let isOpen = false;

    function preload(index) {
        const img = new Image();
        img.src = images[index];
    }

    function open(index) {
        currentIndex = index;
        image.src = images[currentIndex];

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            image.classList.remove('opacity-0', 'scale-95');
            image.classList.add('opacity-100', 'scale-100');
        });

        // preload next
        preload((currentIndex + 1) % images.length);

        document.body.style.overflow = 'hidden';
        isOpen = true;
    }

    function close() {
        modal.classList.add('opacity-0');
        image.classList.remove('opacity-100', 'scale-100');
        image.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            isOpen = false;
        }, 300);
    }

    function swapImage(nextIndex) {
        image.classList.add('opacity-0');

        setTimeout(() => {
            currentIndex = nextIndex;
            image.src = images[currentIndex];
            image.classList.remove('opacity-0');

            // preload next
            preload((currentIndex + 1) % images.length);
        }, 150);
    }

    function next() {
        swapImage((currentIndex + 1) % images.length);
    }

    function prev() {
        swapImage((currentIndex - 1 + images.length) % images.length);
    }

    function init() {
        modal = document.getElementById('galleryModal');
        image = document.getElementById('galleryImage');
        closeBtn = document.getElementById('galleryClose');
        nextBtn = document.getElementById('galleryNext');
        prevBtn = document.getElementById('galleryPrev');

        if (!modal || !image) return;

        thumbs = Array.from(document.querySelectorAll('.screenshot-thumb'));
        images = thumbs.map(el => el.dataset.src);

        thumbs.forEach((el, i) => {
            el.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                open(i);
            });
        });

        // клик по картинке → следующий
        image.addEventListener('click', e => {
            e.stopPropagation();
            next();
        });

        closeBtn.addEventListener('click', close);
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);

        modal.addEventListener('click', e => {
            if (e.target === modal) close();
        });

        document.addEventListener('keydown', e => {
            if (!isOpen) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowRight') next();
            if (e.key === 'ArrowLeft') prev();
        });
    }

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', init)
        : init();

})();
