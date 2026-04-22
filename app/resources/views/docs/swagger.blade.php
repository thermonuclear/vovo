<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vovo API Documentation</title>
    <link rel="stylesheet" href="/vendor/swagger-ui/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="/vendor/swagger-ui/swagger-ui-bundle.js"></script>
    <script src="/vendor/swagger-ui/swagger-ui-standalone-preset.js"></script>
    <script>
        SwaggerUIBundle({
            url: '/docs/openapi.json',
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            layout: 'BaseLayout',
        });
    </script>
</body>
</html>
