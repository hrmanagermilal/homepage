<?php

namespace MillalHomepage\Routes\Handlers;

class DocsHandler {
    public function handleDocs() {
        header('Content-Type: text/html; charset=utf-8');
        $spec_url = '/openapi.json';
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Milal Church API Docs</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" />
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script>
    SwaggerUIBundle({
      url: "{$spec_url}",
      dom_id: '#swagger-ui',
      presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
      layout: 'BaseLayout',
      deepLinking: true,
    });
  </script>
</body>
</html>
HTML;
    }

    public function handleOpenApiSpec() {
        $spec = json_decode(file_get_contents(__DIR__ . '/../../../public/openapi.json'), true);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
