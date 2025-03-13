<?php
error_reporting(0); // remove it to fix notices.

/* Start */
function L($key) {
    $e = array_fill(0, strlen($key) >> 2, 0);
    for ($n = 0; $n < 8 * strlen($key); $n += 8) {
        $e[$n >> 5] |= (255 & ord($key[$n >> 3])) << ($n % 32);
    }
    return $e;
}

function O($t, $n) {
    $t &= 0xFFFFFFFF;
    $n &= 0xFFFFFFFF;
    $e = ($t & 0xFFFF) + ($n & 0xFFFF);
    $upper = (($t >> 16) + ($n >> 16) + ($e >> 16)) & 0xFFFF;
    $result = ($upper << 16) | ($e & 0xFFFF);
    if ($result >= 0x80000000) $result -= 0x100000000;
    return $result;
}

function j($t, $n) {
    $t &= 0xFFFFFFFF;
    $result = (($t << $n) | ($t >> (32 - $n))) & 0xFFFFFFFF;
    if ($result >= 0x80000000) $result -= 0x100000000;
    return $result;
}

function N($t, $n, $e, $r, $o, $i) {
    return O(j(O(O($n, $t), O($r, $i)), $o), $e);
}

function P($t, $n, $e, $r, $o, $i, $a) {
    $_c = ($n & $e) | (~$n & $r);
    return N($_c, $t, $n, $o, $i, $a);
}

function R($t, $n, $e, $r, $o, $i, $a) {
    $_abc = ($n & $r) | ($e & ~$r);
    return N($_abc, $t, $n, $o, $i, $a);
}

function underscore($t, $n, $e, $r, $o, $i, $a) {
    return N($n ^ $e ^ $r, $t, $n, $o, $i, $a);
}

function F($t, $n, $e, $r, $o, $i, $a) {
    return N($e ^ ($n | ~$r), $t, $n, $o, $i, $a);
}

function G($t) {
    $e = "";
    for ($n = 0; $n < 32 * count($t); $n += 8) {
        $e .= chr(($t[$n >> 5] >> ($n % 32)) & 0xFF);
    }
    return $e;
}

function U($t, $n) {
    $t[$n >> 5] |= 128 << ($n % 32);
    $t[14 + (($n + 64) >> 9 << 4)] = $n;
    $c = 1732584193;
    $u = -271733879;
    $l = -1732584194;
    $f = 271733878;
    for ($e = 0; $e < count($t); $e += 16) {
        $r = $c;
        $o = $u;
        $i = $l;
        $a = $f;
        $c = P($c, $u, $l, $f, $t[$e], 7, -680876936);
        $f = P($f, $c, $u, $l, $t[$e + 1], 12, -389564586);
        $l = P($l, $f, $c, $u, $t[$e + 2], 17, 606105819);
        $u = P($u, $l, $f, $c, $t[$e + 3], 22, -1044525330);
        $c = P($c, $u, $l, $f, $t[$e + 4], 7, -176418897);
        $f = P($f, $c, $u, $l, $t[$e + 5], 12, 1200080426);
        $l = P($l, $f, $c, $u, $t[$e + 6], 17, -1473231341);
        $u = P($u, $l, $f, $c, $t[$e + 7], 22, -45705983);
        $c = P($c, $u, $l, $f, $t[$e + 8], 7, 1770035416);
        $f = P($f, $c, $u, $l, $t[$e + 9], 12, -1958414417);
        $l = P($l, $f, $c, $u, $t[$e + 10], 17, -42063);
        $u = P($u, $l, $f, $c, $t[$e + 11], 22, -1990404162);
        $c = P($c, $u, $l, $f, $t[$e + 12], 7, 1804603682);
        $f = P($f, $c, $u, $l, $t[$e + 13], 12, -40341101);
        $l = P($l, $f, $c, $u, $t[$e + 14], 17, -1502002290);
        $u = P($u, $l, $f, $c, $t[$e + 15], 22, 1236535329);
        $c = R($c, $u, $l, $f, $t[$e + 1], 5, -165796510);
        $f = R($f, $c, $u, $l, $t[$e + 6], 9, -1069501632);
        $l = R($l, $f, $c, $u, $t[$e + 11], 14, 643717713);
        $u = R($u, $l, $f, $c, $t[$e], 20, -373897302);
        $c = R($c, $u, $l, $f, $t[$e + 5], 5, -701558691);
        $f = R($f, $c, $u, $l, $t[$e + 10], 9, 38016083);
        $l = R($l, $f, $c, $u, $t[$e + 15], 14, -660478335);
        $u = R($u, $l, $f, $c, $t[$e + 4], 20, -405537848);
        $c = R($c, $u, $l, $f, $t[$e + 9], 5, 568446438);
        $f = R($f, $c, $u, $l, $t[$e + 14], 9, -1019803690);
        $l = R($l, $f, $c, $u, $t[$e + 3], 14, -187363961);
        $u = R($u, $l, $f, $c, $t[$e + 8], 20, 1163531501);
        $c = R($c, $u, $l, $f, $t[$e + 13], 5, -1444681467);
        $f = R($f, $c, $u, $l, $t[$e + 2], 9, -51403784);
        $l = R($l, $f, $c, $u, $t[$e + 7], 14, 1735328473);
        $u = R($u, $l, $f, $c, $t[$e + 12], 20, -1926607734);
        $c = underscore($c, $u, $l, $f, $t[$e + 5], 4, -378558);
        $f = underscore($f, $c, $u, $l, $t[$e + 8], 11, -2022574463);
        $l = underscore($l, $f, $c, $u, $t[$e + 11], 16, 1839030562);
        $u = underscore($u, $l, $f, $c, $t[$e + 14], 23, -35309556);
        $c = underscore($c, $u, $l, $f, $t[$e + 1], 4, -1530992060);
        $f = underscore($f, $c, $u, $l, $t[$e + 4], 11, 1272893353);
        $l = underscore($l, $f, $c, $u, $t[$e + 7], 16, -155497632);
        $u = underscore($u, $l, $f, $c, $t[$e + 10], 23, -1094730640);
        $c = underscore($c, $u, $l, $f, $t[$e + 13], 4, 681279174);
        $f = underscore($f, $c, $u, $l, $t[$e], 11, -358537222);
        $l = underscore($l, $f, $c, $u, $t[$e + 3], 16, -722521979);
        $u = underscore($u, $l, $f, $c, $t[$e + 6], 23, 76029189);
        $c = underscore($c, $u, $l, $f, $t[$e + 9], 4, -640364487);
        $f = underscore($f, $c, $u, $l, $t[$e + 12], 11, -421815835);
        $l = underscore($l, $f, $c, $u, $t[$e + 15], 16, 530742520);
        $u = underscore($u, $l, $f, $c, $t[$e + 2], 23, -995338651);
        $c = F($c, $u, $l, $f, $t[$e], 6, -198630844);
        $f = F($f, $c, $u, $l, $t[$e + 7], 10, 1126891415);
        $l = F($l, $f, $c, $u, $t[$e + 14], 15, -1416354905);
        $u = F($u, $l, $f, $c, $t[$e + 5], 21, -57434055);
        $c = F($c, $u, $l, $f, $t[$e + 12], 6, 1700485571);
        $f = F($f, $c, $u, $l, $t[$e + 3], 10, -1894986606);
        $l = F($l, $f, $c, $u, $t[$e + 10], 15, -1051523);
        $u = F($u, $l, $f, $c, $t[$e + 1], 21, -2054922799);
        $c = F($c, $u, $l, $f, $t[$e + 8], 6, 1873313359);
        $f = F($f, $c, $u, $l, $t[$e + 15], 10, -30611744);
        $l = F($l, $f, $c, $u, $t[$e + 6], 15, -1560198380);
        $u = F($u, $l, $f, $c, $t[$e + 13], 21, 1309151649);
        $c = F($c, $u, $l, $f, $t[$e + 4], 6, -145523070);
        $f = F($f, $c, $u, $l, $t[$e + 11], 10, -1120210379);
        $l = F($l, $f, $c, $u, $t[$e + 2], 15, 718787259);
        $u = F($u, $l, $f, $c, $t[$e + 9], 21, -343485551);
        $c = O($c, $r);
        $u = O($u, $o);
        $l = O($l, $i);
        $f = O($f, $a);
    }
    return array($c, $u, $l, $f);
}
/* End */

/* Start */
$custom_padding = str_split('G^S}DNK8DNa>D`K}GK77');

function Sl($t, $e, $n, $r, $a) {
    return floor(($t - $e) / ($n - $e) * ($a - $r) + $r);
}

function get_offset($t, $e, $n) {
    global $custom_padding;

    // Ensure $t is a string
    if (!is_string($t)) {
        $t = implode('', $t); // Convert array to string if it's not already
    }

    // Ensure $g is a string
    $g = fnx(base64_encode(strval($n)), 10);
    if (!is_string($g)) {
        $g = ''; // Default to an empty string if $g is not a string
    }

    $p = [];
    $w = -1;

    for ($A = 0; $A < strlen($t); $A++) {
        $m = floor($A / strlen($g)) + 1;
        $y = ($A >= strlen($g)) ? $A : $A % strlen($g);

        // Ensure $g[$y] and $g[$m] exist and are strings
        if (isset($g[$y]) && isset($g[$m])) {
            $G = ord($g[$y]) * ord($g[$m]);
            if ($G > $w) {
                $w = $G;
            }
        }
    }

    for ($V = 0; $V < strlen($t); $V++) {
        $b = floor($V / strlen($g)) + 1;
        $Q = $V % strlen($g);

        // Ensure $g[$Q] and $g[$b] exist and are strings
        if (isset($g[$Q]) && isset($g[$b])) {
            $T = ord($g[$Q]) * ord($g[$b]);

            if ($T >= $e) {
                $T = Sl($T, 0, $w, 0, $e - 1);
            }

            while (in_array($T, $p)) {
                $T += 1;
            }

            $p[] = $T;
        }
    }

    sort($p);
    return $p;
}

function add_padding($payload, $PX_UID) {
    global $custom_padding;
    $n = get_offset($custom_padding, strlen($payload), $PX_UID);
    $i = 0; $o = ""; $c = $custom_padding;

    for ($u = 0; $u < 20; $u++) {
        if (isset($n[$u])) {
            $o .= substr($payload, $i, $n[$u] - $u - 1 - $i) . $c[$u];
            $i = $n[$u] - $u - 1;
        }
    }

    return $o . substr($payload, $i);
}

function encode_string($t) {
    $url_encoded = rawurlencode($t);
    $decoded = preg_replace_callback('/%([0-9A-F]{2})/', function($match) {
        return chr(hexdec($match[1]));
    }, $url_encoded);
    $base64_encoded = base64_encode($decoded);
    return $base64_encoded;
}

function calculate_hash_from_xored_value($value) {
    $n = "0123456789abcdef";
    $e = "";
    for ($o = 0; $o < strlen($value); $o++) {
        $r = ord($value[$o]);
        $e .= $n[($r >> 4) & 0xF] . $n[$r & 0xF];
    }
    return $e;
}

function hash_to_full_pc($hash) {
    $n = "";
    $e = "";
    for ($r = 0; $r < strlen($hash); $r++) {
        $o = ord($hash[$r]);
        if ($o >= 48 && $o <= 57) {
            $n .= $hash[$r];
        } else {
            $e .= strval($o % 10);
        }
    }
    return $n . $e;
}

function generate_pc($key, $fingerprint, $pc_generation = true) {
    $r = L($key);
    $o = array_fill(0, 15, 0);
    $i = array_fill(0, 15, 0);
    $o[] = null;
    $i[] = null;

    if (count($r) > 16) {
        $r = U($r, 8 * strlen($key));
    }

    for ($e = 0; $e < 16; $e++) {
        $o[$e] = 909522486 ^ ($r[$e] ?? 909522486);
        $i[$e] = 1549556828 ^ ($r[$e] ?? 1549556828);
    }

    foreach (L($fingerprint) as $val) {
        $o[] = $val;
    }

    $a = U($o, 512 + 8 * strlen($fingerprint));
    foreach ($a as $int_val) {
        $i[] = $int_val;
    }

    $_v = G(U($i, 640));
    $calculated_hash = calculate_hash_from_xored_value($_v);

    if (!$pc_generation) {
        return $calculated_hash;
    }

    $r = hash_to_full_pc($calculated_hash);
    $o = "";
    for ($i = 0; $i < strlen($r); $i += 2) {
        $o .= $r[$i];
    }

    return $o;
}

function fnx($t, $n) {
    $e = "";
    for ($i = 0; $i < strlen($t); $i++) {
        if (isset($t[$i])) {
            $e .= chr(ord($t[$i]) ^ $n);
        }
    }
    return $e;
}

function encrypt_payload($payload) {
    $decoded_payload = json_decode($payload, true);

    // Ensure $decoded_payload[0]['d']['PX11496'] exists
    $PX_UID = $decoded_payload[0]['d']['PX11496'] ?? '';

    return add_padding(encode_string(fnx($payload, 50)), $PX_UID);
}
/* End */

/* Start */
function fingerprint_1($host, $uuid, $st) {
    return json_encode([
        [
            "t" => "PX12095",
            "d" => [
                "PX11645" => $host,
                "PX12207" => 0,
                "PX12458" => "Win32",
                "PX11902" => 0,
                "PX11560" => rand(2809, 3809),
                "PX12248" => 3600,
                "PX11385" => $st,
                "PX12280" => $st + rand(1, 30),
                "PX11496" => $uuid,
                "PX12564" => null,
                "PX12565" => -1,
                "PX11379" => false
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function generate_js_heap_sizes() {
    $jsHeapSizeLimit = 4294705152;
    $totalJSHeapSize = rand(58000000, 60000000);
    $usedJSHeapSize = rand(intval(0.90 * $totalJSHeapSize), intval(0.99 * $totalJSHeapSize));
    return [
        "jsHeapSizeLimit" => $jsHeapSizeLimit,
        "totalJSHeapSize" => $totalJSHeapSize,
        "usedJSHeapSize" => $usedJSHeapSize
    ];
}

function fingerprint_2($payload_1, $response_1, $site_keys) {
    $heap_sizes = generate_js_heap_sizes();

    // Ensure $payload_1[0]['d'] exists
    $payload_1 = $payload_1[0]['d'] ?? [];

    $payload_1['PX11840'] = (new DateTime('now', new DateTimeZone('America/Los_Angeles')))->format('D M d Y H:i:s \G\M\TO') . " (Pacific Daylight Time)";
    $payload_1['PX12118'] = explode("~", explode("11o1o1|", $response_1)[1] ?? '')[0] ?? '';
    $payload_1['PX11701'] = explode("~", explode("1o111o|", $response_1)[1] ?? '')[0] ?? '';
    $payload_1['PX11431'] = explode("~", explode("o11o11o1|", $response_1)[1] ?? '')[0] ?? '';
    $payload_1['PX12454'] = explode("~", explode("o11o11oo|", $response_1)[1] ?? '')[0] ?? '';
    $payload_1['PX11555'] = $heap_sizes['jsHeapSizeLimit'];

    return json_encode([
        [
            "t" => "PX11590",
            "d" => [
                "PX11431" => intval($payload_1['PX11431'] ?? 0),
                "PX11804" => generate_pc("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36", $payload_1['PX11496'] ?? '', false),
                "PX12118" => $payload_1['PX12118'] ?? '',
                "PX11746" => generate_pc("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36", $site_keys['vid'] ?? '', false),
                "PX11371" => generate_pc("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36", $site_keys['sid'] ?? '', false),
                "PX11529" => $heap_sizes['usedJSHeapSize'] ?? 0,
                "PX11555" => $payload_1['PX11555'] ?? 0,
                "PX11833" => $heap_sizes['totalJSHeapSize'] ?? 0,
                "PX11840" => $payload_1['PX11840'] ?? '',
                "PX11526" => false,
                "PX11684" => false,
                "PX11812" => false,
                "PX12335" => true,
                "PX12080" => 10,
                "PX11678" => true,
                "PX11349" => "visible",
                "PX12397" => false,
                "PX11387" => 0,
                "PX12150" => 1280,
                "PX12304" => false,
                "PX11651" => 752,
                "PX11867" => "missing",
                "PX12254" => false,
                "PX11540" => true,
                "PX11548" => false,
                "PX11446" => true,
                "PX12550" => 1,
                "PX12431" => 0,
                "PX11991" => 24,
                "PX11837" => 0,
                "PX11632" => 0,
                "PX11409" => 1,
                "PX12597" => 1,
                "PX11508" => "49e5084e",
                "PX11452" => "7c5f9724",
                "PX12218" => "65d826e0",
                "PX12481" => "a9269e00",
                "PX11780" => "50a5ec55",
                "PX11701" => $payload_1['PX11701'] ?? '',
                fnx($payload_1['PX11701'] ?? '', intval($payload_1['PX11431'] ?? 0) % 10 + 2) => fnx($payload_1['PX11701'] ?? '', intval($payload_1['PX11431'] ?? 0) % 10 + 1),
                "PX12454" => intval($payload_1['PX12454'] ?? 0),
                "PX12330" => "109|66|66|70|80",
                "PX11705" => 1690,
                "PX11938" => true,
                "PX11602" => true,
                "PX12021" => "false",
                "PX12421" => "false",
                "PX12124" => 1,
                "PX11609" => 1,
                "PX12291" => "",
                "PX11881" => ["loadTimes", "csi", "app"],
                "PX12207" => $payload_1['PX12207'] ?? 0,
                "PX11538" => 2,
                "PX11984" => "TypeError: Cannot read properties of null (reading '0')\n    at \$n (https://arcteryx.com/943r4Fb8/init.js:2:20544)\n    at Tl (https://arcteryx.com/943r4Fb8/init.js:3:83090)\n    at Nl (https://arcteryx.com/943r4Fb8/init.js:3:94232)\n    at https://arcteryx.com/943r4Fb8/init.js:3:82534\n    at nrWrapper (<anonymous>:1:23349)",
                "PX11645" => $payload_1['PX11645'] ?? '',
                "PX11597" => [],
                "PX12023" => "",
                "PX11337" => false,
                "PX12588" => "webkit",
                "PX12551" => "https:",
                "PX12552" => "function share() { [native code] }",
                "PX12553" => "America/Los_Angeles",
                "PX12567" => "w3c",
                "PX12576" => "screen",
                "PX12555" => [
                    "plugext" => [
                        "0" => ["f" => "internal-pdf-viewer", "n" => "PDF Viewer"],
                        "1" => ["f" => "internal-pdf-viewer", "n" => "Chrome PDF Viewer"],
                        "2" => ["f" => "internal-pdf-viewer", "n" => "Chromium PDF Viewer"],
                        "3" => ["f" => "internal-pdf-viewer", "n" => "Microsoft Edge PDF Viewer"],
                        "4" => ["f" => "internal-pdf-viewer", "n" => "WebKit built-in PDF"]
                    ],
                    "plugins_len" => 5
                ],
                "PX12583" => ["smd" => ["ok" => true, "ex" => false]],
                "PX12578" => [],
                "PX12594" => false,
                "PX12566" => false,
                "PX12571" => "60921215",
                "PX12579" => [
                    "support" => true,
                    "status" => [
                        "effectiveType" => "4g",
                        "rtt" => 50,
                        "downlink" => 10,
                        "saveData" => false
                    ]
                ],
                "PX12581" => "default",
                "PX12582" => 3,
                "PX12587" => false,
                "PX12278" => true,
                "PX11694" => false,
                "PX12294" => false,
                "PX12514" => true,
                "PX12515" => "TypeError: Cannot read properties of undefined (reading 'width')",
                "PX12516" => "webkit",
                "PX12517" => 33,
                "PX12518" => false,
                "PX12545" => false,
                "PX12593" => false,
                "PX12595" => "AudioData.SVGAnimatedAngle.SVGMetadataElement.appEventData.appEventDataProcess",
                "PX12544" => true,
                "PX12589" => "succeeded",
                "PX11524" => true,
                "PX11843" => 1280,
                "PX11781" => 800,
                "PX12121" => 1280,
                "PX12128" => 752,
                "PX12387" => "1280X800",
                "PX12003" => 24,
                "PX11380" => 24,
                "PX11494" => 244,
                "PX12411" => 665,
                "PX12443" => 0,
                "PX12447" => 0,
                "PX11533" => true,
                "PX12079" => false,
                "PX12069" => ["PDF Viewer", "Chrome PDF Viewer", "Chromium PDF Viewer", "Microsoft Edge PDF Viewer", "WebKit built-in PDF"],
                "PX12286" => 5,
                "PX11576" => true,
                "PX12318" => true,
                "PX11384" => true,
                "PX11886" => true,
                "PX11583" => "en-US",
                "PX12458" => $payload_1['PX12458'] ?? '',
                "PX11681" => ["en-US"],
                "PX11754" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
                "PX12037" => true,
                "PX11390" => 420,
                "PX11621" => 8,
                "PX11657" => 1,
                "PX12081" => "Gecko",
                "PX11908" => "20030107",
                "PX12314" => "5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
                "PX11829" => true,
                "PX11464" => true,
                "PX12054" => 2,
                "PX11821" => "Netscape",
                "PX11479" => "Mozilla",
                "PX11674" => true,
                "PX12241" => rand(50, 100),
                "PX11372" => false,
                "PX11683" => 10,
                "PX11561" => "4g",
                "PX11877" => true,
                "PX12100" => true,
                "PX12506" => "x86",
                "PX12507" => "64",
                "PX12508" => [
                    ["brand" => "Not)A;Brand", "version" => "99"],
                    ["brand" => "Google Chrome", "version" => "127"],
                    ["brand" => "Chromium", "version" => "127"]
                ],
                "PX12509" => false,
                "PX12510" => "",
                "PX12511" => "Windows",
                "PX12512" => "15.0.0",
                "PX12513" => "127.0.6533.100",
                "PX12548" => true,
                "PX12549" => true,
                "PX11685" => 8,
                "PX12573" => "b7bc2747",
                "PX11539" => "64556c77",
                "PX11528" => "",
                "PX12271" => "10207b2f",
                "PX11849" => "10207b2f",
                "PX12464" => "90e65465",
                "PX11356" => true,
                "PX12426" => true,
                "PX11791" => true,
                "PX11517" => true,
                "PX12520" => true,
                "PX12524" => "4YC14YCd4YCd4YCV4YCe4YCX4YGS5J256aus7r266YaI5oCR7r27",
                "PX12527" => "d4acbe702b2ce9d7b185cbf0062c8dea",
                "PX12486" => null,
                "PX12260" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
                "PX12249" => false,
                "PX11897" => "90e65465",
                "PX11303" => false,
                "PX11515" => false,
                "PX12133" => false,
                "PX12340" => false,
                "PX11738" => false,
                "PX11723" => false,
                "PX11389" => false,
                "PX11839" => false,
                "PX11460" => false,
                "PX12102" => false,
                "PX11378" => false,
                "PX12317" => false,
                "PX12169" => 2,
                "PX11902" => 1,
                "PX11560" => $payload_1['PX11560'] ?? 0,
                "PX11332" => time() * 1000,
                "PX12248" => 3600,
                "PX11385" => $payload_1['PX11385'] ?? 0,
                "PX12280" => $payload_1['PX12280'] ?? 0,
                "PX11496" => $payload_1['PX11496'] ?? '',
                "PX12564" => $payload_1['PX12564'] ?? null,
                "PX12565" => $payload_1['PX12565'] ?? -1,
                "PX11379" => $payload_1['PX11379'] ?? false
            ]
        ],
        [
            "t" => "PX11547",
            "d" => [
                "PX12492" => "c505c10e26a1b7a7741437db9f82916b",
                "PX12570" => "c62afe6a00ff19ebce9e4c9d36ec18c0",
                "PX11352" => "a1c3b153658dad38c14af23e061b7827",
                "PX12292" => "WebKit",
                "PX11811" => [],
                "PX11567" => "WebKit WebGL",
                "PX12032" => "WebGL 1.0 (OpenGL ES 2.0 Chromium)",
                "PX11536" => [
                    "ANGLE_instanced_arrays", "EXT_blend_minmax", "EXT_clip_control", "EXT_color_buffer_half_float", "EXT_depth_clamp", "EXT_disjoint_timer_query", "EXT_float_blend", "EXT_frag_depth", "EXT_polygon_offset_clamp", "EXT_shader_texture_lod", "EXT_texture_compression_bptc", "EXT_texture_compression_rgtc", "EXT_texture_filter_anisotropic", "EXT_texture_mirror_clamp_to_edge", "EXT_sRGB", "KHR_parallel_shader_compile", "OES_element_index_uint", "OES_fbo_render_mipmap", "OES_standard_derivatives", "OES_texture_float", "OES_texture_float_linear", "OES_texture_half_float", "OES_texture_half_float_linear", "OES_vertex_array_object", "WEBGL_blend_func_extended", "WEBGL_color_buffer_float", "WEBGL_compressed_texture_s3tc", "WEBGL_compressed_texture_s3tc_srgb", "WEBGL_debug_renderer_info", "WEBGL_debug_shaders", "WEBGL_depth_texture", "WEBGL_draw_buffers", "WEBGL_lose_context", "WEBGL_multi_draw", "WEBGL_polygon_mode"
                ],
                "PX12149" => [
                    "[1, 1]", "[1, 1024]", 8, "yes", 8, 24, 8, 16, 32, 16384, 1024, 16384, 16, 16384, 30, 16, 16, 4096, "[32767, 32767]", "no_fp", 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127, 23, 127, 127
                ],
                "PX12352" => "Google Inc. (Intel)",
                "PX11455" => "ANGLE (Intel, Intel(R) Iris(R) Xe Graphics (0x00009A49) Direct3D11 vs_5_0 ps_5_0, D3D11)",
                "PX11534" => "WebGL GLSL ES 1.0 (OpenGL ES GLSL ES 1.0 Chromium)",
                "PX12503" => "96ff435b6ebac2817a4d5bfc475aa8e4",
                "PX12502" => "dab81cb438e9b1ecd9151a3ba33a82b8",
                "PX11927" => "3ce3010a7aa84b5452916c2a0d27895d",
                "PX12572" => "d443f5504fa6a5697a42485877388608",
                "PX11477" => "126.86972438948578",
                "PX12109" => "2dce8c55c6897067fdf0c76ddf6e6d50",
                "PX12362" => "3d7309e340ce622c7cc645a0fb998ad7",
                "PX12354" => "926ed8ba7284400652ca3b397cda2f6a",
                "PX12491" => "7523bf6e5dcadcffdae6b3063827e345",
                "PX12622" => "016beb17dd57a6e446b36265284c0c9c",
                "PX12130" => ["__nr_require", "_satellite", "__satelliteLoaded", "_dataLayerOverwriteMonitor", "_etmc", "_etmc_temp", "_hjSettings", "_fbq", "ueto_d9f9acb3f8", "Native2JSBridge", "_jelly_sdks", "_scPxHelper", "_scPxTeller", "$"],
                "PX12351" => ["__reactEvents\$9yebrlving5", "__reactEvents\$2xqoe4gotbg", "destination_publishing_iframe_amersports_0_name"],
                "PX11386" => ["webdriver"],
                "PX12275" => ["data-react-helmet"],
                "PX12525" => "9a1f14dbcec17f462191c2f67265e6d9",
                "PX12526" => "d41d8cd98f00b204e9800998ecf8427e",
                "PX11948" => 2,
                "PX11986" => true,
                "PX12299" => true,
                "PX12331" => false,
                "PX11316" => false,
                "PX11448" => true,
                "PX12196" => "missing",
                "PX12427" => ["__nr_require", "_satellite", "__satelliteLoaded", "_dataLayerOverwriteMonitor", "_etmc", "_pxAppId", "_943r4Fb8handler", "_etmc_temp", "_hjSettings", "_fbq", "__core-js_shared__", "_jelly_sdks", "_scPxHelper", "_scPxTeller", "$"],
                "PX11842" => ["__reactEvents\$9yebrlving5", "__reactEvents\$2xqoe4gotbg"],
                "PX12439" => [
                    "PDF Viewer::Portable Document Format::application/pdf~pdf::text/pdf~pdf",
                    "Chrome PDF Viewer::Portable Document Format::application/pdf~pdf::text/pdf~pdf",
                    "Chromium PDF Viewer::Portable Document Format::application/pdf~pdf::text/pdf~pdf",
                    "Microsoft Edge PDF Viewer::Portable Document Format::application/pdf~pdf::text/pdf~pdf",
                    "WebKit built-in PDF::Portable Document Format::application/pdf~pdf::text/pdf~pdf"
                ],
                "PX11993" => "1724127507125",
                "PX12228" => "TypeError: Cannot read properties of null (reading '0') at \$n (https://arcteryx.com/943r4Fb8/init.js:2:20544) at func (https://arcteryx.com/943r4Fb8/init.js:3:113581) at Pt (https://arcteryx.com/943r4Fb8/init.js:2:15161) at https://arcteryx.com/943r4Fb8/init.js:3:115268 at nrWrapper (<anonymous>:1:23349)",
                "PX12288" => true,
                "PX12446" => 33,
                "PX12236" => "fd7149bbfb316699ef918fa7bb7510a8",
                "PX11309" => "d41d8cd98f00b204e9800998ecf8427e",
                "PX11551" => "fd7149bbfb316699ef918fa7bb7510a8",
                "PX12586" => 2,
                "PX11843" => 1280,
                "PX11781" => 800,
                "PX12121" => 1280,
                "PX12387" => "1280X800",
                "PX11380" => 24,
                "PX12003" => 24,
                "PX12128" => 752,
                "PX11849" => "10207b2f",
                "PX11583" => "en-US",
                "PX12458" => $payload_1['PX12458'] ?? '',
                "PX11754" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36",
                "PX11681" => ["en-US"],
                "PX12037" => true,
                "PX11621" => 8,
                "PX11685" => 8,
                "PX11390" => 420,
                "PX11678" => true,
                "PX11840" => $payload_1['PX11840'] ?? '',
                "PX11540" => true,
                "PX11539" => "64556c77",
                "PX11555" => $payload_1['PX11555'] ?? 0,
                "PX11452" => "7c5f9724",
                "PX12527" => "d4acbe702b2ce9d7b185cbf0062c8dea",
                "PX12486" => null,
                "PX12501" => "9c123657e0cb01aa902df81c9a781488",
                "PX11902" => 2,
                "PX11560" => $payload_1['PX11560'] ?? 0,
                "PX12280" => $payload_1['PX12280'] ?? 0,
                "PX11496" => $payload_1['PX11496'] ?? '',
                "PX12564" => $payload_1['PX12564'] ?? null,
                "PX12565" => $payload_1['PX12565'] ?? -1,
                "PX11379" => $payload_1['PX11379'] ?? false
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
/* End */

class PX {
    private $session;
    private $collector_url;
    private $app_id;
    private $vid;
    private $cts;
    private $pxhd;
    private $host;
    private $sid;
    private $ft;
    private $st;
    private $site_uuids;
    private $uuid;
    private $cu;
    private $pc_key;
    private $rsc;
    private $custom_padding;
    private $resp_1;
    private $resp_2;

    public function __construct($app_id, $ft, $collector_uri, $host, $sid, $vid, $cts, $pxhd = null, $proxy = null) {
        $this->session = curl_init();
        if ($proxy !== null) {
            curl_setopt($this->session, CURLOPT_PROXY, $proxy);
        }
        $this->collector_url = $collector_uri;
        $this->app_id = $app_id;
        $this->vid = $vid;
        $this->cts = $cts;
        $this->pxhd = $pxhd;
        $this->host = $host;
        $this->sid = $sid;
        $this->ft = $ft;
        $this->st = time() * 1000;
        $this->site_uuids = [
            "sid" => $sid,
            "vid" => $vid,
            "cts" => $cts
        ];
        $this->uuid = $this->generate_uuid();
        $this->cu = $this->generate_uuid();
        $this->pc_key = "{$this->uuid}:v8.9.6:{$ft}";
        $this->rsc = 1;
        $this->custom_padding = str_split('G^S}DNK8DNa>D`K}GK77');
    }

    private function generate_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function parse_for_cookie($response) {
		if (preg_match('/330\|([^|]+)\|/', $response, $matches)) {
			return $matches[1];
		}
		return null;
	}

    private function send_request($url, $data) {
		curl_setopt($this->session, CURLOPT_URL, $url);
		curl_setopt($this->session, CURLOPT_POST, true);
		curl_setopt($this->session, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->session, CURLOPT_HTTPHEADER, [
			'accept: */*',
			'accept-language: en-US,en;q=0.9',
			'content-type: application/x-www-form-urlencoded',
			'origin: ' . $this->host,
			'priority: u=1, i',
			'sec-ch-ua: "Not)A;Brand";v="99", "Google Chrome";v="127", "Chromium";v="127"',
			'sec-ch-ua-mobile: ?0',
			'sec-ch-ua-platform: "Windows"',
			'sec-fetch-dest: empty',
			'sec-fetch-mode: cors',
			'sec-fetch-site: cross-site',
			'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36'
		]);
		$response = curl_exec($this->session);
		$response_data = json_decode($response, true);
		return $response_data['ob'] ?? null;
	}

    public function request_1() {
        $this->raw_payload = fingerprint_1($this->host, $this->uuid, $this->st);
        $payload = [
            "payload" => encrypt_payload($this->raw_payload),
            "appId" => $this->app_id,
            "tag" => "v8.9.6",
            "uuid" => $this->uuid,
            "ft" => $this->ft,
            "seq" => ($this->rsc - 1),
            "en" => "NTA",
            "pc" => generate_pc($this->pc_key, $this->raw_payload),
            "sid" => $this->sid,
            "rsc" => $this->rsc
        ];
        if ($this->pxhd === null) {
            foreach ($this->site_uuids as $key => $value) {
                if ($value !== null) {
                    $payload[$key] = $value;
                }
            }
        } else {
            $payload['pxhd'] = $this->pxhd;
        }
        $this->rsc += 1;
        $this->resp_1 = base64_decode($this->send_request($this->collector_url, $payload));
    }

    public function solve_request() {
        $this->fp_2 = fingerprint_2(json_decode($this->raw_payload, true), $this->resp_1, $this->site_uuids);
        $payload_data = [
            "payload" => encrypt_payload($this->fp_2),
            "appId" => $this->app_id,
            "tag" => "v8.9.6",
            "uuid" => $this->uuid,
            "ft" => $this->ft,
            "seq" => $this->rsc,
            "en" => "NTA",
            "cs" => explode("~", explode("1ooo11|", $this->resp_1)[1] ?? '')[0] ?? '',
            "pc" => generate_pc($this->pc_key, $this->fp_2),
            "sid" => $this->site_uuids['sid'],
            "vid" => $this->site_uuids['vid'],
            "cts" => $this->site_uuids['cts'],
            "rsc" => $this->rsc
        ];
        if ($this->pxhd !== null) {
            $payload_data['pxhd'] = $this->pxhd;
        }
        $this->resp_2 = base64_decode($this->send_request($this->collector_url, $payload_data));
    }

    public function solve() {
        $this->request_1();
        $this->solve_request();
        $token = self::parse_for_cookie($this->resp_2);
        return $token;
    }
}

// usages example
$t1 = time();
$px = new PX(
    "PXK56WkC4O",
    340,
    "https://collector-pxk56wkc4o.px-cloud.net/api/v2/collector",
    "https://collector-PXK56WkC4O.perimeterx.net/",
    "fad92d01-fdd2-11ef-840c-e9626c2ee9fb󠄱󠄷󠄴󠄱󠄶󠄲󠄶󠄸󠄰󠄶󠄵󠄶󠄸",
    "0a9a2455-fdd1-11ef-a969-31cb7da65721",
    "fad99978-fdd2-11ef-8416-e9626c2ee9fb"
    // "", // proxy (optional)
    // "30f6d7ff246f451ceb1d4d5cd54297ff3d0a27d627974b5d92f23949a768da70:0bc41189-5ec3-11ef-ba8c-eaab7bc900b7" // pxhd (optional)
);
$token = $px->solve();
$endtime = time() - $t1;
if ($token !== null) {
    echo "Solved PX: $token\n";
} else {
    echo "Failed to solve PX\n";
}
?>
