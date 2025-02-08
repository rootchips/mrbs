<!DOCTYPE html>
<html>
<head>
    <title>Google Auth</title>
</head>
<body>
      <script>
      const googleData = @json($data);

      if (window.opener) {
            window.opener.postMessage(googleData, "{{ config('app.web') }}");
            window.close();
      } else {
            console.error("No parent window detected.");
      }
      </script>
    <p>Authenticating, please wait...</p>
</body>
</html>