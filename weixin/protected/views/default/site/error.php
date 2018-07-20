<?php
$this->title = $title;
?>
<html>
<head>
    <title><?= $title ?></title>
</head>
<body>
<div class="row" style="margin: auto; width: 1024px;">
    <div class="col-md-12">
        <div class="text-center">
            <h2><?= $title ?></h2>
            <div>
                <?= $trace ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
