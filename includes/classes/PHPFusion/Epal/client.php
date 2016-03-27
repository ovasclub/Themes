<?php
/*----------------------------------------+
 * PHP-Fusion Community Software Client
 *----------------------------------------+
 * Copyright 2015 (c) PHP-Fusion Inc.
 * All rights reserved.
 * ---------------------------------------+
 * Filename: client.php
 * ---------------------------------------+
 * Redistributions of source code must retain
 * the above copyright notice, this list of
 * conditions and the following disclaimer.
 *----------------------------------------+
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT
 * HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER
 * IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *-----------------------------------------*/

namespace PHPFusion\Epal;

use PHPFusion\Epal\resource\Model;

require_once CLASSES."PHPFusion/Epal/resource/model.php";

require_once CLASSES."PHPFusion/Epal/viewer/viewer.php";

class client extends Model {

    private $package;

    private static $cert_server_path = "http://next.php-fusion.co.uk/RC7/infusions/license/api/v1/certificates/";

    private static $activate_server_path = "http://next.php-fusion.co.uk/RC7/infusions/license/api/v1/activation/";

    /**
     * @param mixed $package
     */
    private function registerPackage($package) {
        $this->package = $package;
    }

    function __construct($package = 0) {

        if ($package) {
            $this->registerPackage($package);
        }

        self::set_variables();

        if ($this->package_validation()) {

            $this->display_client();
        }
    }


    public function display_client() {

        $serial_key = array(
            "key" => "",
            "password" => "",
            "serial" => "",
        );

        if (isset($_POST['get_serial'])) {

            $serial_key = $this->cURL_certificate();

        } else if (isset($_POST['send_serial'])) {

            $serial_key = $this->cURL_register();

            redirect(FUSION_REQUEST);

        }

        if ($this->package_validation()) {

            return true;

        } else {

            new \PHPFusion\Epal\viewer\viewer($serial_key);

        }

        return FALSE;
    }



    public function package_validation() {
        return TRUE;

        $certificate_file = CLASSES."PHPFusion/Epal/vault/certificate.php";

        $locale = $this->get_locale();

        if (!file_exists($certificate_file)) {

            return FALSE;

        } else {

            include $certificate_file;

            if (!empty($package) && is_array($package)) {

                foreach($package as $serial_key => $package_data) {

                    if ($package_data['license'] == $this->package) {

                        if ($package_data['activation_end'] > 0) {

                            if (time() > $package_data['activation_end']) {

                                $expired_error = str_replace ( "[SERIAL_KEY]", $serial_key, $locale['epc_error_8']);

                                addNotice("danger", $expired_error);

                                return FALSE;

                            }

                        } else if (isset($package_data['activation_code'])) {

                            $private_key = $this->getHost().$serial_key.$package_data['activation_time'].$package_data['activation_end'].$package_data['activation'];

                            $tmp = base64_decode($package_data['activation_code']);

                            $rand = substr($tmp, 0, 10);

                            $signature = substr($tmp, 10);

                            $test = substr(hash_hmac('sha256', $rand, $private_key, true), 0, 10);

                            return $test === $signature;

                        } else {
                            return FALSE;
                        }

                    }
                }
                return FALSE;
            }
            return FALSE;
        }
    }

    /**
     * do cURL to authenticate from licensing server rest api
     */
    private function cURL_register() {

        $locale = $this->get_locale();

        $path = CLASSES."PHPFusion/Epal/vault/";

        if (!file_exists($path."certificate.php")) {
            touch($path."certificate.php");
        }

        include $path."certificate.php";

        $serial_params = array(

            "serial_key" => form_sanitizer($_POST['serial_key'], "", "serial_key"),

            "password" => form_sanitizer($_POST['password'], "", "password")

        );

        if (!empty($package[$serial_params['serial_key']])) {

            $serial_params += $package[$serial_params['serial_key']];

        }

        $server_address = self::$activate_server_path;

        $cUrl_http_query = $server_address . http_build_query($serial_params, NULL, "&");

        $curl = curl_init($cUrl_http_query);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($response === FALSE) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            addNotice("danger", $locale['epc_error_1']);
        }
        curl_close($curl);

        $decoded = json_decode($response);

        if (!empty($decoded->error)) {

            addNotice("danger", $decoded->error);

        } else {

            $fRead = file($path."certificate.php");

            if (!is_null($fRead)) {

                $certificate_line_offset = 0;
                foreach($fRead as $line => $file_text) {
                    if (stristr($file_text, $serial_params['serial_key'])) {
                        $certificate_line_offset = $line;
                    }
                }

                if ($certificate_line_offset) {

                    $lines = array(
                        "activation" => $certificate_line_offset + 3,
                        "activationTime" => $certificate_line_offset + 4,
                        "activationCode" => $certificate_line_offset + 5
                    );

                    // Update activation to true
                    if (stristr($fRead[$lines['activation']], "activation")) {
                        $fRead[$lines['activation']] = str_replace($fRead[$lines['activation']], '"activation" => "'.$decoded->activation.'",'.PHP_EOL, $fRead[$lines['activation']]);
                    } else {
                        addNotice("danger", $locale['epc_error_2']);
                    }

                    // Update activation time
                    if (stristr($fRead[$lines['activationTime']], "activation_time")) {
                        $fRead[$lines['activationTime']] = str_replace($fRead[$lines['activationTime']],
                                                                       '"activation_time" => "'.$decoded->activation_time.'",'.PHP_EOL.'"activation_end" => "'.$decoded->activation_end.'",'.PHP_EOL.'',
                                                                       $fRead[$lines['activationTime']]);
                    } else {
                        addNotice("danger", $locale['epc_error_3']);
                    }

                    // Update activation code
                    if (stristr($fRead[$lines['activationCode']], ");")) {

                        $fRead[$lines['activationCode']] = str_replace($fRead[$lines['activationCode']], '"activation_code" => "'.$decoded->activation_code.'",'.PHP_EOL.");".PHP_EOL, $fRead[$lines['activationCode']]);

                    } else {

                        addNotice("danger", $locale['epc_error_4']);

                    }

                    if (!empty($fRead) && is_array($fRead)) {

                        // remove php for eval
                        unset($fRead[0]);

                        $fileBuffer = implode("", $fRead);

                        $result = @eval($fileBuffer . "; return true;");

                        if ($result) {

                            $fileBuffer = "<?php".PHP_EOL.$fileBuffer;

                            file_put_contents($path."certificate.php", $fileBuffer);

                            addNotice("success", $locale['epc_success_1']);

                        } else {

                            addNotice("danger", $locale['epc_error_5']);

                        }

                    }

                } else {

                    addNotice("danger", $locale['epc_error_6']);

                }

            } else {
                addNotice("danger", $locale['epc_error_7']);
            }

        }

        return ($serial_params);
    }

    /**
     * do cURL to get certificate from licensing server rest api
     * - can send back the response to touch a license.txt file with the details
     * - will it be good to like a install -sh
     *
     */
    private function cURL_certificate() {

        //* Sample URL : http://localhost/php-fusion/infusions/license/api/v1/certificates/password=12345678&key=3bqE+Ah+M/IFfja5ctJJPLAhwt4=
        $locale = $this->get_locale();

        $server_address = self::$cert_server_path;

        $cURL_params = array(
            "key" => $_POST['key'],
            "password" => stripinput($_POST['password'])
        );
        $cUrl_http_query = $server_address."password=".$cURL_params['password']."&key=".$cURL_params['key'];

        $curl = curl_init($cUrl_http_query);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        if ($response === FALSE) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            addNotice("danger", $locale['epc_error_1']);
        }
        curl_close($curl);

        $decoded = json_decode($response);

        if (!empty($decoded->error)) {

            addNotice("danger", $decoded->error);

        } else {

            addNotice("success", $locale['epc_success_2']);

            $cURL_params['serial_key'] = $decoded->certificate_private_key;

            /**
             * Generate a certificate on this authentication
             */
            $path = CLASSES."PHPFusion/Epal/vault/";

            if (!file_exists($path)) {
                mkdir($path, 0755, TRUE);
            }

            if (!file_exists($path."certificate.php")) {

                if (file_exists($path."_certificate.php") && function_exists("rename")) {

                    @rename($path."_certificate.php", "certificate.php");

                } else {

                    touch($path."certificate.php");

                    $file = file_get_contents($path."certificate.php");

                    include $path."certificate.php";
                }

            }

            $file_add = '$package["'.$decoded->certificate_private_key.'"] = array('.PHP_EOL;
            $file_add .= '"domain" => "'.$this->getHost().'",'.PHP_EOL;
            $file_add .= '"license" => "'.$decoded->certificate_licenseid.'",'.PHP_EOL;
            $file_add .= '"activation" => "0",'.PHP_EOL;
            $file_add .= '"activation_time" => "'.time().'"'.PHP_EOL;
            $file_add .= ');'.PHP_EOL;

            if (!empty($file) && !isset($package[$decoded->certificate_id])) {
                $file_ = $file . $file_add;
            } else {
                $file_ = "<?php".PHP_EOL;
                $file_ .= $file_add;
            }

            file_put_contents($path."certificate.php", $file_);

            unset($file_);
        }

        return $cURL_params;
    }

    public static function getHost() {

        $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');

        $sourceTransformations = array(
            "HTTP_X_FORWARDED_HOST" => function($value) {
                $elements = explode(',', $value);
                return trim(end($elements));
            }
        );

        $host = '';
        foreach ($possibleHostSources as $source)
        {
            if (!empty($host)) break;
            if (empty($_SERVER[$source])) continue;
            $host = $_SERVER[$source];
            if (array_key_exists($source, $sourceTransformations))
            {
                $host = $sourceTransformations[$source]($host);
            }
        }

        $host = preg_replace('/:\d+$/', '', $host);

        return stripinput(trim($host));
    }
}