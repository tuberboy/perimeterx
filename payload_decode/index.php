<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Tuber Boy">
    <title>Payload Encoder/Decoder - LocalSEO</title>
    <meta name="msapplication-TileColor" content="#786fff">
    <meta name="theme-color" content="#786fff">
    <link rel="shortcut icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/styles.css?v=0.0.1">
</head>
<body>
    <div class="header"><a href="/user">Back [&#x21A9;</a></div>
<?php
class PerimeterXPayloadDecoder {
    private $uuid;
    private $sts;

    public function __construct($uuid, $sts = "") {
        $this->uuid = $uuid;
        $this->sts = empty($sts) ? "1604064986000" : $sts;
    }

    private function xorString($input, $key) {
        $output = "";
        for ($i = 0; $i < strlen($input); $i++) {
            $output .= chr(ord($input[$i]) ^ $key);
        }
        return $output;
    }

    private function valueMapping($t, $e, $n, $r, $o) {
        return (int) floor((($t - $e) / ($n - $e)) * ($o - $r) + $r);
    }

    private function decode($t) {
        return base64_encode($t);
    }

    private function reverseEv($e, $t, $n) {
        $r = $this->xorString($this->decode($n), 10); // Matches reverseIe(decode(n), 10)
        $o = [];
        $a = -1;

        for ($i = 0; $i < strlen($e); $i++) {
            $c = floor($i / strlen($r) + 1);
            $l = $i >= strlen($r) ? $i % strlen($r) : $i;
            $u = ord($r[$l]) * ord($r[$c % strlen($r)]);

            if ($u > $a) {
                $a = $u;
            }
        }

        for ($h = 0; $h < strlen($e); $h++) {
            $l = floor($h / strlen($r)) + 1;
            $s = $h % strlen($r);
            $Z = ord($r[$s]) * ord($r[$l % strlen($r)]);

            if ($Z >= $t) {
                $Z = $this->valueMapping($Z, 0, $a, 0, $t - 1);
            }

            while (in_array($Z, $o)) {
                $Z += 1;
            }
            $o[] = $Z;
        }

        sort($o);
        return $o;
    }

    private function reverseFa($t, $r, $n) {
        $splitPayload = [];
        $o = 0;

        for ($i = 0; $i < strlen($t); $i++) {
            $current = $n[$i] - $i - $o;
            if ($current > 0) {
                array_splice($splitPayload, $o, 0, str_split(substr($r, 0, $current)));
                $r = substr($r, $current);
            }
            $o = $n[$i] - $i - 1;
        }

        // Match JavaScript: slice off last t.length characters and append r
        $reorderedPayload = substr(implode("", $splitPayload), 0, -strlen($t)) . $r;
        $base6Payload = base64_decode($reorderedPayload); // Matches Buffer.from(..., "base64").toString("ascii")

        return $this->xorString($base6Payload, 50); // Final XOR with 50
    }

    public function deobfuscate($encodedPayload) {
        $reverseFv = $this->xorString($this->decode($this->sts), 10); // Matches reverseIe(decode(newsts), 10)
        $reverseEv = $this->reverseEv($reverseFv, strlen($encodedPayload) - 20, $this->uuid);
        return $this->reverseFa($reverseFv, $encodedPayload, $reverseEv);
    }
}

if(isset($_POST["post"])) {
	$payload = str_replace(" ", "+", $_POST["pl"]);
	$uuid = $_POST["uuid"];
	$sts = $_POST["sts"]; // Optional, will default to "1604064986000"

	$decoder = new PerimeterXPayloadDecoder($uuid, $sts);
	$decodedPayload = $decoder->deobfuscate($payload);

	echo '<div class="box"><div class="title">Decoded PerimeterX Payload</div><textarea type="text" name="pl" placeholder="Enter payload data *" autocomplete="off">'.$decodedPayload.'</textarea></div>';
}
?>
    <div class="box">
        <div class="title">Decode PerimeterX Payload</div>
        <form method="post">
	    <textarea type="text" name="pl" placeholder="Enter payload data *" autocomplete="off" required></textarea>
            <input type="text" name="uuid" placeholder="Enter uuid *" value="" required>
            <input type="text" name="sts" placeholder="Optional, will default to "1604064986000"" autocomplete="off">
            <input type="checkbox" class="vanish-checkbox" id="vanishCheckbox">
            <div class="vanish-button-container">
                <button name="post"><label for="vanishCheckbox">DECODE</label></button>
                <div class="please-wait">Please wait...<span><i></i><i></i><i></i></span></div>
            </div>
        </form>
    </div>
    <div class="footer">&copy; Copyright <?php echo date("Y"); ?> - <a href="/">LocalSEO</a></div>
</body>
</html>
