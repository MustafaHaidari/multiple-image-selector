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
    .custom-file-preview img {
  transition: transform 0.3s ease-in-out;
}

.custom-file-preview img:hover {
  transform: scale(1.5);
}
  </style>
</head>
<body>

<div class="container">
  <h2>Upload your images</h2>
  <form action="index.php" method="post" enctype="multipart/form-data">
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
  const clearButton = document.getElementById("clearButton");
  let selectedFiles = [];

  function makeDraggable(img) {
    img.draggable = true;

    img.addEventListener('dragstart', function (e) {
      e.dataTransfer.setData('text/plain', this.src);
    });
  }

  function updatePreview() {
    previewContainer.innerHTML = "";
    for (let i = 0; i < selectedFiles.length; i++) {
      const file = selectedFiles[i];
      const img = document.createElement("img");
      img.classList.add('fade-in');
      img.src = URL.createObjectURL(file);
      makeDraggable(img);
      previewContainer.appendChild(img);
    }
  }

  clearButton.addEventListener("click", function() {
    previewContainer.innerHTML = '<span class="drop-text">Drag and Drop</span>';
    selectedFiles = [];
  });

  previewContainer.addEventListener("click", function() {
    fileInput.click();
  });

  fileInput.addEventListener("change", function() {
    selectedFiles = [...selectedFiles, ...Array.from(fileInput.files)];
    updatePreview();
    fileInput.value = null; // Reset input field to allow the same image to be selected multiple times
  });

  // Drag and Drop functionality for the file input
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

    const newFiles = e.dataTransfer.files;
    selectedFiles = [...selectedFiles, ...Array.from(newFiles)];
    updatePreview();
  });

  // Drag and Drop functionality for rearranging images
  previewContainer.addEventListener('dragover', function (e) {
    e.preventDefault();
    e.stopPropagation();
  });

  previewContainer.addEventListener('drop', function (e) {
    if (e.target.tagName.toLowerCase() === 'img') {
      e.preventDefault();
      e.stopPropagation();

      const src = e.dataTransfer.getData('text/plain');
      const draggedImg = document.querySelector(`img[src="${src}"]`);
      this.insertBefore(draggedImg, e.target.nextSibling);
    }
  });
});

</script>

<?php
// Database connection
if (isset($_POST['submit'])) {
  $host = "localhost";
  $dbname = "your_database";
  $username = "root";
  $password = "password";
  $conn = new mysqli($host, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $fileCount = count($_FILES['files']['name']);
  for ($i = 0; $i < $fileCount; $i++) {
    $fileName = $_FILES['files']['name'][$i];
    $fileTmpName = $_FILES['files']['tmp_name'][$i];
    $fileDestination = 'uploads/' . $fileName;
    move_uploaded_file($fileTmpName, $fileDestination);

    $sql = "INSERT INTO images (image_name, image_path) VALUES ('$fileName', '$fileDestination')";
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
