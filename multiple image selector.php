<!DOCTYPE html>
<html>
<head>
  <title>Stylish Multiple Image Selection</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .custom-file-container {
      position: relative;
      margin: 20px 0;
    }
    .custom-file-preview {
      border: 3px dashed #ccc;
      padding: 50px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      border-radius: 10px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .custom-file-preview.dragging {
      border: 3px dashed #007bff;
      background: rgba(0, 123, 255, 0.1);
    }
    .custom-file-preview img {
      max-width: 100px;
      margin: 5px;
      border-radius: 5px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
      
    }
    .drop-text {
      display: inline;
      font-size: 18px;
      color: #ccc;
    }
    .fade-in {
      animation: fadeIn ease 1s;
    }

    @keyframes fadeIn {
      0% {opacity:0;}
      100% {opacity:1;}
    }
    .custom-file-preview img:hover {
      transform: scale(2.5);
      z-index: 1;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Upload your images</h2>
  <form action="test5.php" method="post" enctype="multipart/form-data">
    <div class="custom-file-container">
      <input type="file" name="files[]" id="customFile" multiple class="d-none">
      <div class="custom-file-preview" id="filePreview">
        <span class="drop-text">Drag and Drop</span>
      </div>
    </div>
    <button type="button" id="clearButton" class="btn btn-danger mt-2">Clear</button>
    <button type="submit" name="submit" class="btn btn-success mt-2">Upload</button>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const fileInput = document.getElementById("customFile");
  const previewContainer = document.getElementById("filePreview");
  const dropText = document.querySelector(".drop-text");
  const clearButton = document.getElementById("clearButton");
  let selectedFiles = [];

  clearButton.addEventListener("click", function() {
    previewContainer.innerHTML = '<span class="drop-text">Drag and Drop</span>';
    selectedFiles = [];
  });

  previewContainer.addEventListener("click", function() {
    fileInput.click();
  });

  function updatePreview() {
    previewContainer.innerHTML = "";
    if (selectedFiles.length > 6) {
      alert("You can only select up to 6 images.");
      selectedFiles = selectedFiles.slice(0, 6); // Keep only the first 6 files
    }
    for (let i = 0; i < selectedFiles.length; i++) {
      const file = selectedFiles[i];
      const img = document.createElement("img");
      img.src = URL.createObjectURL(file);
      previewContainer.appendChild(img);
    }
  }

  fileInput.addEventListener("change", function() {
    const newFiles = Array.from(fileInput.files);
    selectedFiles = [...selectedFiles, ...newFiles];
    updatePreview();
  });

  // Drag and Drop
  previewContainer.addEventListener("dragover", function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.add("dragging");
  });

  previewContainer.addEventListener("dragleave", function(e) {
    this.classList.remove("dragging");
  });

  previewContainer.addEventListener("drop", function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.classList.remove("dragging");

    const newFiles = Array.from(e.dataTransfer.files);
    selectedFiles = [...selectedFiles, ...newFiles];
    updatePreview();
  });
});

</script>
<?php
// Database connection
if (isset($_POST['submit'])) {
  $host = "localhost";
  $dbname = "testimage";
  $username = "root";
  $password = "";
  $conn = new mysqli($host, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $fileCount = count($_FILES['files']['name']);
  for ($i = 0; $i < $fileCount; $i++) {
    $fileName = $_FILES['files']['name'][$i];
    $fileTmpName = $_FILES['files']['tmp_name'][$i];
    $fileDestination = 'images/shoes' . $fileName;
    move_uploaded_file($fileTmpName, $fileDestination);

    $sql = "INSERT INTO images () VALUES ('','$fileName', '$fileDestination')";
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully<br>";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }

  $conn->close();
}
?>
</body>
</html>
