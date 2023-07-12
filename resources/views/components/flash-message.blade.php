<style>
    .alert {
        transition: opacity 0.5s;
        opacity: 1;
    }

    .alert.hide {
        opacity: 0;
    }
</style>
<div class="text-center" style="position: absolute; z-index: 999; width: 100%;">
    @if ($message = Session::get('success'))
        <div id="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Something's wrong!</strong> You should check in on some of those fields below.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($message = Session::get('warning'))
        <div id="warningMessage" class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($message = Session::get('info'))
        <div id="infoMessage" class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div id="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach($errors->all() as $error)
                <span class="fst-italic text-danger"> {{ $error }} </span>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set the timeout for each message
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.classList.add('hide');
                successMessage.addEventListener('transitionend', function() {
                    successMessage.remove();
                });
            }

            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.classList.add('hide');
                errorMessage.addEventListener('transitionend', function() {
                    errorMessage.remove();
                });
            }

            var warningMessage = document.getElementById('warningMessage');
            if (warningMessage) {
                warningMessage.classList.add('hide');
                warningMessage.addEventListener('transitionend', function() {
                    warningMessage.remove();
                });
            }

            var infoMessage = document.getElementById('infoMessage');
            if (infoMessage) {
                infoMessage.classList.add('hide');
                infoMessage.addEventListener('transitionend', function() {
                    infoMessage.remove();
                });
            }
        }, 1250);
    });
</script>
