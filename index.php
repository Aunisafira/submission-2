<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=storageapri;AccountKey=VA8Vg2LbHGTdWu1xSKxouQfLegcJITR2C6TbfmHx7mH6AbXv1JP2Bgo1Il/3uW3JrkZTu1bzO1MxJLP3k+l6kw==;EndpointSuffix=core.windows.net";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

$containerName = "blob-picture";

if (isset($_POST['submit'])) {
    $filetoupload = $_FILES["fileToUpload"]["name"];
    $content =fopen($_FILES["fileToUpload"]["tmp_name"],"r");
    echo fread($content,filesize($filetoupload));

    $blobClient->createBlockBlob($containerName, $filetoupload, $content);
    header("Location: index.php");
}

$listBlobOptions = new ListBlobsOptions();
$listBlobOptions->setPrefix("");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Azure Image Vision</title>
</head>
<body>


<div class="container">
        <h2>Analisa gambar dengan azure computer vision</h2>
        <form action="index.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
			<input class="form-control" type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
        </div>
        <div class="form-group">
            <input type="submit" name="submit" value="Unggah" class="btn btn-primary">
        </div>
        </form>

        <div class="col-md-12">
            <table class="table">
                <thead>
                    <td>Nama</td>
                    <td>Thumbnail</td>
                    <td>Aksi</td>
                </thead>
                <tbody>
                <?php
                do {
                    $result = $blobClient->listBlobs($containerName, $listBlobOptions);
                    foreach ($result->getBlobs() as $key) {
                ?>
                <tr>
                    <td><?php echo $key->getName(); ?></td>
                    <td>
                        <img src="<?php echo $key->getUrl(); ?>" alt="" class="img-thumbnail" width="150px">
                    </td>
                    <td><a href="analitic.php?images=<?php echo $key->getUrl(); ?>" class="btn btn-xs btn-success">Analisa</a></td>
                </tr>
                <?php
                    } $listBlobOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>