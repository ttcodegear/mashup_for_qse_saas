<?php
//require_once '../vendor/autoload.php';
require_once 'C:\home\tts\php-7.4.30-x64\vendor\autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$privateKey = file_get_contents('c:\temp\privatekey.pem');

$kid = 'my-custom-jwt';
$iat = time();
$exp = $iat + 3600;
$payload = [
  'jti' => uniqid(),
  'iss' => 'https://my-custom-jwt',
  'aud' => 'qlik.api/login/jwt-session',
  'iat' => $iat,
  'exp' => $exp,
  'nbf' => $iat,
  'sub' => '0hEhiPyhMBdtOCv2UZKoLo4G24p-7R6eeGdZUQHF0-c',
  'subType' => 'user',
  'name' => 'Hardcore Harry',
  'email' => 'harry@example.com',
  'email_verified' => true,
  'groups' => ['Administrators', 'Sales', 'Marketing']
];

$jwt = JWT::encode($payload, $privateKey, 'RS256', $kid);
?>

<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <script src="https://tts.sg.qlikcloud.com/resources/assets/external/requirejs/require.js"></script>
  <link rel="stylesheet" href="https://tts.sg.qlikcloud.com/resources/autogenerated/qlik-styles.css">

<script>
//Qlik Sense SaaSのテナント情報
var server = {
  host: "tts.sg.qlikcloud.com", // Qlik Sense SaaS tenant
  port: 443,                    // Qlik Sense SaaS port
  prefix: "/",                  // Virtual Proxy prefix
  isSecure: true,               // true=https, false=http
  webIntegrationId: 'TnClSNKbs_QqoZa31Qwy1qD5U1CotVfF', // Web ID
  jwt: '<?php echo($jwt) ?>'    // JWT
};

//認証済みかのチェック
function connect() {
  var tenant = "https://"+server.host+(server.port ? ":"+server.port : "");
  //ログイン状態チェック用API: https://[tenant]/api/v1/users/me
  fetch(tenant + "/api/v1/users/me", {
    method: "GET",
    mode: "cors",           // no-cors, cors, same-origin
    credentials: "include", // include, same-origin, omit
    headers: {
      "qlik-web-integration-id": server.webIntegrationId
    }
  }).then(response => {
    //未認証の場合はJWTで認証
    if(response.status===401) {
      fetch(tenant + "/login/jwt-session", {
        method: 'POST',
        mode: 'cors',
        credentials: 'include',
        headers: {
          'qlik-web-integration-id': server.webIntegrationId,
          'Authorization':'Bearer '+server.jwt
        },
      }).then((response) => {
        if(response.status !== 200) {
          alert('Failed to login via JWT');
        }
        else {
          start();
        }
      });
    }
    else {
      start();
    }
  }).catch(error => {
    alert(error);
  });
}
connect(); //認証済みかのチェック

function start() {
require.config({
  // https://tts.sg.qlikcloud.com/resources
  baseUrl: "https://" + server.host + (server.port ? ":" + server.port : "") + server.prefix + "resources",
  webIntegrationId: server.webIntegrationId
});
require(["js/qlik"], function(qlik) {
  qlik.setLanguage('ja-JP');                           // 表示言語
  qlik.theme.apply('breeze');                          // Theme ID
  var app_id = '72a3da4b-1093-4c4c-840d-1ee44fbcbb91'; // App ID
  var app = qlik.openApp(app_id, server);
  app.getObject('QV01', 'CurrentSelections'); // 選択バー
  app.getObject('QV02', 'BDQru');             // フィルターパネル
  app.getObject('QV03', 'CzcvAzC');           // フィルターパネル
  app.getObject('QV04', 'pmGDmg');            // 棒チャート

  window.onbeforeunload = function() {
    app.close();
  }
});
}
</script>

</head>

<body>

<table border="1" width="100%" height="100%">
  <tbody>
    <tr>
      <td colspan="3" width="100%" height="10%"><div id="QV01" style="width:100%;height:100%;"></div></td>
    </tr>
    <tr>
      <td width="30%" height="45%"><div id="QV02" style="width:100%;height:100%;"></div></td>
      <td colspan="2" rowspan="2" width="70%" height="80%"><div id="QV04" style="width:100%;height:100%;"></div></td>
    </tr>
    <tr>
      <td width="30%" height="45%"><div id="QV03" style="width:100%;height:100%;"></div></td>
    </tr>
  </tbody>
</table>

<script>
</script>

</body>
</html>
