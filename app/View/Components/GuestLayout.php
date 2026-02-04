<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public function render(): View
    {
        // ВАЖНО: рендерим ИМЕННО компонентный layout
        return view('components.guest-layout');
    }
}
