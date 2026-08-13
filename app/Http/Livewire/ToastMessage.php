<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ToastMessage extends Component
{
    public const STACK_KEY = 'toasts';

    /**
     * @var array
     */
    public $toasts;

    protected $listeners = ['newToastMessage' => 'newMessage', 'closeToast','refreshToasts'];

    public function mount()
    {
        $this->toasts = [];
    }

    /**
     * @param array $newMessage
     * @return void
     */
    public function newMessage(array $newMessage)
    {
        session()->push(static::STACK_KEY, $newMessage + [
                'time' => now(),
                'id' => md5(time().$newMessage['message']),
            ]);
        $this->toasts = session()->get(static::STACK_KEY);
    }

    public function render()
    {
        $this->refreshToasts();
        return view('livewire.toast-message');
    }

    public function refreshToasts()
    {
        $this->toasts = session()->get(static::STACK_KEY, []);
        foreach($this->toasts as $key => $toast) {
            if (now()->diffInMilliseconds($toast['time']) > 2500) {
                unset($this->toasts[$key]);
            }
        }
        $this->toasts = array_values($this->toasts);
        session()->put(ToastMessage::STACK_KEY, $this->toasts);
    }

    public function closeToast(string $id)
    {
        if (!session()->has(ToastMessage::STACK_KEY)) {
            return;
        }
        $this->stack = session()->get(ToastMessage::STACK_KEY);
        foreach($this->stack as $key => $message) {
            if ($message['id'] == $id) {
                unset($this->stack[$key]);
            }
        }
        $this->stack = array_values($this->stack);
        session()->put(ToastMessage::STACK_KEY, $this->stack);
    }
}
