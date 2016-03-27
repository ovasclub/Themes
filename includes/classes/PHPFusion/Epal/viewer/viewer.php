<?php
/*----------------------------------------+
 * PHP-Fusion Community Software Client
 *----------------------------------------+
 * Copyright 2015 (c) PHP-Fusion Inc.
 * All rights reserved.
 * ---------------------------------------+
 * Filename: viewer.php
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

namespace PHPFusion\Epal\viewer;

use PHPFusion\Epal\resource\Model;

class viewer extends Model {

    public function __construct($serial) {

        $locale = $this->get_locale();

        $aidlink = $this->get_aidlink();

        $serial_key = $serial;

        add_to_head("<link rel='stylesheet' href='".CLASSES."PHPFusion/Epal/viewer/styles.css' type='text/css' />");
        ?>
        <section id="epalclient" class="console_page">
            <div class="console_bg">
                <div class="console_wrapper">
                    <div class="console_logo">
                        <img alt="<?php echo fusion_get_settings("sitename") ?>"
                             src="<?php echo IMAGES."php-fusion-logo.png" ?>"/>

                        <h3><?php echo $locale['epc_100'] ?></strong></h3>
                    </div>
                    <div class="console_panel">
                        <?php
                        if (!\defender::safe()) {
                            setNotice('danger', $locale['global_182']);
                        }
                        $notices = getNotices();
                        echo renderNotices($notices);

                        $form_action = FUSION_SELF.$aidlink == ADMIN."index.php".$aidlink ? FUSION_SELF.$aidlink."&amp;pagenum=0" : FUSION_SELF."?".FUSION_QUERY;

                        if (!empty($serial_key['serial_key']) or isset($_POST['send_serial'])) {

                            echo openform('serialkeyform', 'post', $form_action);
                            echo form_textarea('serial_key',$locale['epc_104'], $serial_key['serial_key'], array("placeholder"=>$locale['epc_105']));
                            echo form_text('password', $locale['epc_106'], $serial_key['password'], array("placeholder"=>$locale['epc_107'], "type"=>"password", "required"=>true));
                            echo form_button('send_serial', $locale['epc_108'], 'send_serial',
                                             array('class' => 'btn-primary btn-block'));
                            echo closeform();

                        } else {

                            echo openform('userkeyform', 'post', $form_action);
                            echo form_text('key', $locale['epc_109'], $serial_key['key'], array("placeholder"=>$locale['epc_110'], "required"=>true));
                            echo form_text('password', $locale['epc_106'], $serial_key['password'], array("placeholder"=>$locale['epc_107'], "type"=>"password", "required"=>true));
                            echo form_button("get_serial", $locale['epc_111'], "get_serial", array("class"=>"btn-primary"));
                            echo closeform();

                        }
                        ?>
                    </div>

                    <small class='text-alt'><?php echo $locale['version'].fusion_get_settings('version') ?></small>
                    <div class="clearfix m-t-10 m-b-10">
                        <?php echo $locale['epc_103'] ?>
                    </div>
                    <?php echo $locale['epc_102'] ?>
                </div>
            </div>
        </section>
        <?php
        }
}