<div class="input-group mb-1">
    <span class="input-group-text">De</span>
    <input type="datetime-local" class="form-control" wire:model.change="{{ $wire }}.from"/>
</div>
<div class="input-group">
    <span class="input-group-text">Até</span>
    <input type="datetime-local" class="form-control" wire:model.change="{{ $wire }}.to"/>
</div>
