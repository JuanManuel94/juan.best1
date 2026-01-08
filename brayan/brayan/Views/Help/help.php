<?php
function getReadmeFromGitHub() {
  $url = "https://api.github.com/repos/walterrengifoxd/softwareWISP/contents/README.md";

  $opts = [
    "http" => [
      "method" => "GET",
      "header" => [
        "User-Agent: MyApp",
        "Accept: application/vnd.github.v3+json"
      ]
    ]
  ];

  $context = stream_context_create($opts);
  $response = file_get_contents($url, false, $context);

  if ($response === FALSE) {
    return "No tienes permisos (error de conexión).";
  }

  $data = json_decode($response, true);

  if (!isset($data['content'])) {
    return "No disponible (sin contenido).";
  }

  return base64_decode($data['content']);
}
?>

<?php head($data); ?>
<div class="panel panel-default">
  <div class="panel-body border-panel">
    <div class="row">
      <div class="col-md-12 col-sm-12 col-12 text-center">

        <!-- Logo centrado -->
<?php
if (!empty($_SESSION['businessData']['logotyope'])) {
  if ($_SESSION['businessData']['logotyope'] == "superwisp.png") {
    $logofac = base_style() . '/images/logotypes/' . $_SESSION['businessData']['logotyope'];
  } else {
    $logofac_url = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
    if (@getimagesize($logofac_url)) {
      $logofac = base_style() . '/uploads/business/' . $_SESSION['businessData']['logotyope'];
    } else {
      $logofac = base_style() . '/images/logotypes/superwisp.png';
    }
  }
} else {
  $logofac = base_style() . '/images/logotypes/superwisp.png';
}
?>
<img src="<?= $logofac ?>" width="200px" class="mb-4 d-block mx-auto">


        <!-- Contenido README estilizado -->
        <div style="
          background-color: #f9f9f9;
          border: 1px solid #ddd;
          border-radius: 16px;
          padding: 40px;
          margin: 0 auto;
          max-width: 1000px;
          text-align: left;
          font-family: 'Segoe UI', sans-serif;
          font-size: 16px;
          line-height: 1.8;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        ">
          <?= nl2br(markdownToHtml(getReadmeFromGitHub())) ?>
        </div>

      </div>
    </div>
  </div>
</div>
<?php footer($data); ?>

<?php
// Convierte el Markdown del README a HTML básico
function markdownToHtml($markdown) {
  // Encabezados
  $markdown = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $markdown);
  $markdown = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $markdown);
  $markdown = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $markdown);

  // Negritas
  $markdown = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $markdown);

  // Cursivas
  $markdown = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $markdown);

  // Enlaces
  $markdown = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank">$1</a>', $markdown);

  // Líneas horizontales
  $markdown = preg_replace('/---/', '<hr>', $markdown);

  // Listas
  $markdown = preg_replace('/^\s*-\s*(.+)$/m', '<li>$1</li>', $markdown);
  $markdown = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $markdown);

  return $markdown;
}
?>
