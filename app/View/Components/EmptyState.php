<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmptyState extends Component
{
    public string $title;
    public string $message;
    public string $button;
    public string $link;
    public string $alt;
    /**
     * Create a new component instance.
     */
    public function __construct(string $title, string $message, string $button, string $link, string $alt = 'Sem dados')
    {
        $this->title = $title;
        $this->message = $message;
        $this->button = $button;
        $this->link = $link;
        $this->alt = $alt;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.empty-state');
    }
}
