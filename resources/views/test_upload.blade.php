<!DOCTYPE html>
<html>
<head>
    <title>CSV Upload Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>CSV Upload Test</h1>
    <form id="testForm" enctype="multipart/form-data">
        @csrf
        <input type="file" id="testFile" name="csv_file" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>

    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            const formData = new FormData(this);
            const fileInput = document.getElementById('testFile');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select a file');
                return;
            }
            
            console.log('File selected:', fileInput.files[0].name);
            
            fetch('/admin/crops/import', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                alert('Response: ' + JSON.stringify(data));
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        });
    </script>
</body>
</html>