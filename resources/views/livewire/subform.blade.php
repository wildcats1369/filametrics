<div>
    @foreach($fields as $field => $type)
        @if($type === 'text')
            <div>
                <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                <input type="text" id="{{ $field }}" wire:model.defer="account.{{ $field }}">
            </div>
        @elseif($type === 'upload')
            <div>
                <label for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                <input type="file" id="{{ $field }}" wire:model.defer="account.{{ $field }}">
            </div>
        @endif
    @endforeach
</div>