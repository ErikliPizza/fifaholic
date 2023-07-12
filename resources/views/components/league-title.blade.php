@props(['title', 'id'])

<div style="cursor: pointer;" class="text-center p-1 my-1 bg-dark text-white rounded"
    {{ isset($id) ? 'onclick=window.location=\'/league/'.$id.'\'' : '' }}
>
    {{ $title }}
</div>
