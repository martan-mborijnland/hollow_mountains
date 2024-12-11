<?php use App\Utility\Functions; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet/less" type="text/css" href="websrc/styles.less" />
    <script src="https://cdn.jsdelivr.net/npm/less" ></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title><?= Functions::convertToTitle($_GET['page']).' | ' ?? '' ?>Hollow Mountains</title>
</head>
<body>