<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <title>Phrasio - AI Title Generator</title>
</head>

<body>
    @include('loader')
    <div class="hidden flex flex-col items-center justify-center min-h-screen bg-gray-100 sm:items-center py-4 sm:pt-0"
        id="content">
        <div class="max-w-6xl w-full mx-auto sm:px-12 lg:px-8 space-y-4 py-4">
            <div class="text-center text-gray-800 py-4">
                <center>
                    <a href="/"><img src="{{ asset('icon.png') }}" height="200" width="200"></a>
                </center>
                <h1 class="text-7xl font-bold">PhrasioAI - Title Generator</h1>
            </div>

            <div class="w-full rounded-md bg-white border-2 p-4 min-h-[60px] h-full text-gray-600" id="titleForm">
                <form id="generateForm" class="inline-flex gap-2 w-full">
                    <input name="title" class="w-full outline-none text-2xl" value="{{ $title }}"
                        placeholder="What's your research title about?" id="title" />
                    <button class="rounded-xl bg-gray-900 px-4 py-2 text-white" onclick="startVoice()" id="voice_search"
                        type="button" title="Voice Search">
                        <i class="fa fa-microphone"></i>
                    </button>
                    <button class="rounded-xl bg-emerald-500 px-4 py-2 text-white" id="generateBtn"
                        title="Generate Title">
                        Generate
                    </button>
                </form>
            </div>
            <div id="voice_recognition_text" class="text-gray-900 mt-20"></div>
            <div id="title_error" class="hidden text-red-500 mt-20"></div>
            <div id="result"></div>
        </div>
    </div>
</body>

@include('components.footer')

</html>

<script>
    $(window).on("load", () => {
        setTimeout(() => {
            $("#loader").fadeOut(500);
            $('#content').removeClass('hidden');
            $('#content').fadeIn();
            $('#footer').removeClass('hidden');
            $('#footer').fadeIn();
        }, 1000);
    });
    $(document).ready(function() {
        $('#title').keyup(function(e) {
            if ($(this).val().length > 0) {
                $('#titleForm').removeClass('border-red-500');
                $('#title_error').addClass('hidden');
                $('#title_error').html("");
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#generateBtn').click(function(e) {
            e.preventDefault();
            let generateForm = $('#generateForm')[0];
            let generateFormData = new FormData(generateForm);
            $(this).prop('disabled', true);
            $('#voice_search').addClass('hidden');
            $('#voice_recognition_text').html("");
            $(this).html("<i class='fa fa-spinner fa-spin'></i>");
            $.ajax({
                type: "post",
                url: '/writer/generate',
                data: generateFormData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: (res) => {
                    $(this).prop('disabled', false);
                    $('#voice_search').removeClass('hidden');
                    $(this).html('Generate');
                    $('#titleForm').removeClass('border-1 border-red-500');
                    if (res.content) {
                        new swal({
                            title: 'Success',
                            text: 'Title Generated Successfully!',
                            icon: 'success'
                        });
                        $('#result').html(
                            '<div class="w-full rounded-md bg-white border-2 p-4 min-h-[300px] h-full text-gray-600 mb-40">' +
                            '<textarea class="min-h-[400px] h-full w-full outline-none text-gray-900 font-semibold " spellcheck="false" style="resize:none;" id="typed_result"></textarea>' +
                            '</div>'
                        );

                        var typed = new Typed('#typed_result', {
                            strings: [res.content],
                            typeSpeed: 0,
                            startDelay: 1000,
                            loop: false,
                        });
                    } else {
                        $('#result').html(
                            '<div class="w-full rounded-md bg-white border-2 p-4 min-h-[300px] h-full text-gray-600 mb-40">' +
                            '<center><h1 class="text-3xl font-bold">No Result Found</h1></center>' +
                            '</div>'
                        );
                    }
                },
                error: (err) => {
                    $(this).prop('disabled', false);
                    $('#voice_search').removeClass('hidden');
                    $(this).html('Generate');
                    if (err.status === 422) {
                        $('#titleForm').addClass('border-red-500');
                        $('#title_error').removeClass('hidden');
                        $('#title_error').html(err.responseJSON.errors.title[0]);
                    }

                    if (err.status === 500) {
                        new swal({
                            title: 'Error',
                            text: 'Something went wrong',
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });

    function startVoice() {
        let speech = true;
        let voice_recognition_text = document.getElementById("voice_recognition_text");
        window.SpeechRecognition = window.webkitSpeechRecognition;
        const recognition = new SpeechRecognition();
        recognition.interimResults = true;

        recognition.onstart = function(e) {
            voice_recognition_text.innerHTML = "Please Speak To Search!";
        };

        recognition.onspeechend = function(e) {
            voice_recognition_text.innerHTML = "";
        };

        recognition.onerror = function(e) {
            voice_recognition_text.innerHTML = "Something Went Wrong! Try Again!";
        };

        recognition.onresult = function(e) {
            const transcript = Array.from(e.results)
                .map(result => result[0])
                .map(result => result.transcript);
            document.getElementById('title').value = transcript;
        };

        if (speech == true) {
            recognition.start();
        }
    }
</script>
