<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;

new class extends Component {

    public $header;
    public $content;
    public $is_confirm;
    public $is_hidden;
    public $okName = '';
    public $cancelName = '';

    public $callbackOK = '';

    public $callbackCancel = '';

    public function boot()
    {
        $this->okName = __('inputs.ok');
        $this->cancelName = __('inputs.cancel');
    }

    public function mount()
    {
        $this->header = __('dialog.header');
        $this->content = __('dialog.content');
        $this->is_confirm = true;
        $this->is_hidden = true;
    }

    public function closeDialog()
    {
        $this->is_hidden = true;
    }

    public function okClicked()
    {
        $this->is_hidden = true;
        if (!empty($this->callbackOK)) {
            $this->dispatch($this->callbackOK);
        } else {
            Log::info('It is still empty');
        }
    }

    public function cancelClicked()
    {
        $this->is_hidden = true;
        if (!empty($this->callbackCancel)) {
            $this->dispatch($this->callbackCancel);
        }
    }

    #[On('show-dialog')]
    public function showDialog($params)
    {
        $validator = Validator::make($params, [
            'callbackOK' => 'string',
            'callbackCancel' => 'string',
            'title' => 'string',
            'message' => 'string|required',
            'ok' => 'string',
            'cancel' => 'string',
        ]);

        if ($validator->valid()) {
            Log::info('After validation', [$params, $validator->valid()]);
            $valid = $validator->valid();
            $params = $valid['params'];
            if (!empty($params['callbackOK'])) {
                $this->callbackOK = $params['callbackOK'];
                Log::info('Set callback', [$this->callbackOK]);
            } else {
                $this->callbackOK = '';
            }
            if (!empty($params['callbackCancel'])) {
                $this->callbackCancel = $params['callbackCancel'];
            } else {
                $this->callbackCancel = '';
            }

            if (!empty($params['title'])) {
                $this->header = $params['title'];
            } else {
                $this->header = '';
            }
            $this->content = $params['message'];

            if (!empty($params['ok'])) {
                $this->okName = $params['ok'];
            }

            if (!empty($params['cancel'])) {
                $this->okName = $params['cancel'];
            }

            $this->is_hidden = false;
        }
    }
};

?>

<div wire:keydown.escape.window="closeDialog"
     wire:click.outside="closeDialog">
    <div class="dialog" @if($is_hidden) style="display:none" @endif>
        <div class="dialog__header">
            <div class="dialog__header_header">{{ $header }}</div>
            <div class="dialog__header_close" wire:click="closeDialog">X</div>
        </div>
        <div class="dialog__content">
            {!! $content !!}
        </div>
        <div class="dialog__footer">
            <button class="dialog__footer__button btn primary dialog__footer__button__ok" type="button"
                    wire:click="okClicked">{{ $okName }}
            </button>
            @if($is_confirm)
                <button class="dialog__footer__button btn default dialog__footer__button__cancel" type="button"
                    wire:click="cancelClicked">{{ $cancelName }}
                </button>
            @endif
        </div>
    </div>
</div>
