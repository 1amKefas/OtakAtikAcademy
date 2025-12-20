<textarea 
  id="{{ $id ?? 'tinymce-' . uniqid() }}"
  name="{{ $name ?? 'content' }}"
  class="tinymce-editor w-full"
  @if($value ?? false) data-content="{{ base64_encode($value) }}" @endif
>{{ $slot ?? '' }}</textarea>
