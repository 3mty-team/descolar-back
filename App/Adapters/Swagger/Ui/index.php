<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Descolar Swagger</title>
    <link rel="stylesheet" type="text/css" href="./App/Adapters/Swagger/Ui/assets/css/swagger-ui.css"/>
    <link rel="stylesheet" type="text/css" href="./App/Adapters/Swagger/Ui/assets/css/index.css"/>

    <link rel="icon" type="image/png" href="./App/Adapters/Swagger/Ui/assets/icons/favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="./App/Adapters/Swagger/Ui/assets/icons/favicon-16x16.png" sizes="16x16"/>
</head>

<body>
<div id="swagger-ui"></div>
<script>
    <?php
    /** @var string $url */
    ?>
    let url = "<?= $url ?>";
</script>
<script src="./App/Adapters/Swagger/Ui/assets/js/swagger-ui-bundle.js" charset="UTF-8"></script>
<script src="./App/Adapters/Swagger/Ui/assets/js/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
<script src="./App/Adapters/Swagger/Ui/assets/js/swagger-initializer.js" charset="UTF-8"></script>
</body>
</html>
