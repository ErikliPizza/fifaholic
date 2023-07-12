@extends('layouts.app')
@section('content')
    <x-board-setting>
        <h1 class="text-center">Access Tokens</h1>
        <p class="text-center text-secondary">Save your access token before leave the page, you won't be able to see your token again.</p>

        <form method="POST" action="{{ route('tokens.store') }}">
            @csrf
            <div class="input-group">
                <input type="password" id="inputField" class="form-control" value="@if (session()->has('access_token')){{ session('access_token') }}@endif" readonly>
                @if (session()->has('access_token'))
                    <button id="copyButton" class="btn btn-sm btn-success" data-token-id="your-token-id">Copy to Clipboard</button>
                @else
                    <button type="submit" class="btn btn-sm btn-dark">Create</button>
                @endif
            </div>
        </form>
        <!-- Initialize the copy to clipboard functionality -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var submitButton = document.getElementById('copyButton');
                submitButton.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent form submission
                });
                var copyButton = document.getElementById('copyButton');
                copyButton.addEventListener('click', function () {
                    // Get the input field value
                    var inputValue = document.getElementById('inputField').value;
                    // Get the token ID from the button's data attribute
                    // Concatenate the input value and token ID
                    var textToCopy = inputValue;

                    // Create a temporary textarea element
                    var textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    textarea.style.position = 'fixed';
                    textarea.style.top = '0';
                    textarea.style.left = '0';
                    textarea.style.opacity = '0';

                    // Append the textarea to the DOM
                    document.body.appendChild(textarea);

                    // Select the text in the textarea
                    textarea.select();
                    textarea.setSelectionRange(0, textarea.value.length);

                    // Copy the selected text to the clipboard
                    document.execCommand('copy');

                    // Remove the temporary textarea from the DOM
                    document.body.removeChild(textarea);

                    copyButton.innerHTML = 'Copied!';
                });
            });
        </script>
    </x-board-setting>
@endsection
