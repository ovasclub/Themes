<?php
/*----------------------------------------+
 * PHP-Fusion Community Software Client
 *----------------------------------------+
 * Copyright 2015 (c) PHP-Fusion Inc.
 * All rights reserved.
 * ---------------------------------------+
 * Filename: resource/model.php
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
namespace PHPFusion\Epal\resource;

abstract class Model {

    protected static $aidlink;

    protected static $userdata;

    protected static $locale;

    public static function get_locale($key = NULL) {
        return $key === NULL ? self::$locale : (isset(self::$locale[$key]) ? self::$locale[$key] : NULL);
    }


    public static function get_aidlink() {
        return self::$aidlink;
    }

    public static function get_userdata() {
        return self::$userdata;
    }

    protected function set_variables() {

        global $userdata, $aidlink, $locale;

        include CLASSES."PHPFusion/Epal/locale/English.php";

        if (!empty($locale) && empty(self::$locale)) {
            foreach($locale as $locale_key => $locale_value) {
                self::$locale[$locale_key] = $locale_value;
            }
        }

        self::$userdata = $userdata;

        self::$aidlink = $aidlink;
    }

}