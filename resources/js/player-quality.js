document.querySelectorAll('[data-quality]').forEach(btn => {
    btn.addEventListener('click', () => {

        if (btn.disabled) {
            window.dispatchEvent(new CustomEvent('premium:cta', {
                detail: {
                    reason: 'quality_limit'
                }
            }));
            return;
        }

        const quality = btn.dataset.quality;

        console.log('switch quality to', quality);
        // HLS.js auto-level или manual (позже)
    });
});
