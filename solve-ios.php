<?php
// textnow ios PerimeterX Solver
class PerimeterXSolver {
    private $appId = "PXK56WkC4O";
    private $userAgent = "Mozilla/5.0 (iPhone; CPU iPhone OS 15_8_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko)";
    private $postUrl = "https://collector-pxk56wkc4o.px-cloud.net/assets/js/bundle";
    private $getUrl = "https://collector-pxk56wkc4o.px-client.net/b/g";
    private $uuid;
    private $vid;
    private $cts;
    private $sid;
    private $cs;
    private $ci;
    private $seq = 0;

    public function __construct($uuid, $vid) {
        $this->uuid = $uuid;
        $this->vid = $vid;
        $this->cts = $this->generateCTS();
    }

    private function generateCTS() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function generateProofOfChallenge() {
        return substr(md5($this->cts . $this->uuid . $this->appId), 0, 16);
    }

    private function deriveKey() {
        return substr(md5($this->appId . $this->cts), 0, 8);
    }

    private function xorEncrypt($input, $key) {
        $output = '';
        for ($i = 0; $i < strlen($input); $i++) {
            $output .= chr(ord($input[$i]) ^ ord($key[$i % strlen($key)]));
        }
        return $output;
    }

    private function generatePayload($data) {
        $json = json_encode($data);
        $b64 = base64_encode($json);
        $key = $this->deriveKey();
        $encrypted = $this->xorEncrypt($b64, $key);
        return base64_encode($encrypted);
    }

    private function sendRequest($url, $method, $fields) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . ($method === 'GET' ? '?' . http_build_query($fields) : ''));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: */*",
            "Content-Type: application/x-www-form-urlencoded",
            "Origin: https://perimeterx.net",
            "Referer: https://perimeterx.net/",
            "User-Agent: " . $this->userAgent,
            "Accept-Language: en-US,en;q=0.9",
            "Accept-Encoding: "
        ]);
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ["error" => "HTTP Error: $httpCode", "body" => $body];
        }
        return ["data" => json_decode($body, true)];
    }

    public function solve() {
        $ts = time();
        $baseData = [
            "appId" => $this->appId,
            "uuid" => $this->uuid,
            "vid" => $this->vid,
            "ft" => 340,
            "seq" => $this->seq,
            "en" => "NTA",
            "pc" => $this->generateProofOfChallenge(),
            "cts" => $this->cts,
            "ts" => $ts,
            "ua" => $this->userAgent,
            "tag" => "v9.2.7",
            "rsc" => 1
        ];

        // 1st Request (POST, seq=0)
        $payload = $this->generatePayload($baseData);
        $response = $this->sendRequest($this->postUrl, 'POST', array_merge($baseData, ["payload" => $payload]));
        if (isset($response['error']) || !isset($response['data']['do']) || ($response['data']['do'] !== null && !empty($response['data']['do']))) {
            return ["success" => false, "step" => "POST seq=0", "response" => $response];
        }
        $this->seq++;

        // Set fallback values (strip Unicode artifacts)
        $this->sid = $this->sid ?? "4b2fb1c6-fe82-11ef-a280-22197c1fa43d";
        $this->cs = $this->cs ?? "7f73e1723e010ad56a7afb410d6b5a7ca9af4ed7ff5963a59204b070d4237852";
        $this->ci = $this->ci ?? "4b36a250-fe82-11ef-8397-0f93f6583196";

        // 2nd Request (GET, seq=1)
        $baseData["seq"] = $this->seq;
        $baseData["cs"] = $this->cs;
        $baseData["sid"] = $this->sid;
        $baseData["ci"] = $this->ci;
        $baseData["rsc"] = 2;
        $payload = $this->generatePayload($baseData);
        $response = $this->sendRequest($this->getUrl, 'GET', array_merge($baseData, ["payload" => $payload]));
        if (isset($response['error']) || !isset($response['data']['do']) || (!empty($response['data']['do']) && $response['data']['do'] !== [])) {
            return ["success" => false, "step" => "GET seq=1", "response" => $response];
        }
        $this->seq += 2;

        // 3rd Request (POST, seq=3)
        $baseData["seq"] = $this->seq;
        $baseData["rsc"] = 2;
        $payload = $this->generatePayload($baseData);
        $response = $this->sendRequest($this->postUrl, 'POST', array_merge($baseData, ["payload" => $payload]));
        if (isset($response['error']) || !isset($response['data']['do']) || ($response['data']['do'] !== null && !empty($response['data']['do']))) {
            return ["success" => false, "step" => "POST seq=3", "response" => $response];
        }
        $this->seq++;

        // 4th Request (POST, seq=4)
        $baseData["seq"] = $this->seq;
        $baseData["rsc"] = 3;
        $payload = $this->generatePayload($baseData);
        $response = $this->sendRequest($this->postUrl, 'POST', array_merge($baseData, ["payload" => $payload]));
        if (isset($response['error']) || !isset($response['data']['do']) || ($response['data']['do'] !== null && !empty($response['data']['do']))) {
            return ["success" => false, "step" => "POST seq=4", "response" => $response];
        }
        $this->seq++;

        // 5th Request (POST, seq=5)
        $baseData["seq"] = $this->seq;
        $baseData["rsc"] = 4;
        $payload = $this->generatePayload($baseData);
        $response = $this->sendRequest($this->postUrl, 'POST', array_merge($baseData, ["payload" => $payload]));
        if (isset($response['error']) || !isset($response['data']['do']) || ($response['data']['do'] !== null && !empty($response['data']['do']))) {
            return ["success" => false, "step" => "POST seq=5", "response" => $response];
        }

        return ["success" => true, "integrity_token" => $this->sid];
    }
}

$solver = new PerimeterXSolver($uuid, $vid);
$solverResult = $solver->solve();

var_dump($solverResult);
?>
