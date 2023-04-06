<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <script src="https://apis.google.com/js/api.js"></script>
<button onclick="pickFile()">Select file from Google Drive</button>
<script>
    function pickFile() {
        gapi.load('picker', {
            callback: function() {
                var picker = new google.picker.PickerBuilder()
                    .addView(google.picker.ViewId.DOCS)
                    .setOAuthToken('{{ $token['access_token']}}')
                    .setDeveloperKey('AIzaSyBTRl5UeBlIOtsqz6HfpjxIXfDq1f85qjM')
                    .setCallback(pickFileCallback)
                    .build();
                picker.setVisible(true);
            }
        });
    }

    function pickFileCallback(data) {
        if (data.action == google.picker.Action.PICKED) {
            $.ajax({
                method: 'POST',
                url: '{{ route('pick-file') }}',
                data: {
                    fileId: data.docs[0].id,
                    token: '{{ json_encode($token) }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.success);
                }
            });
        }
    }
</script>

</body>
</html>