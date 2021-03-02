<?php

class SMS {

    var $host;
    var $port;
    /*
     * Username that is to be used for submission
     */
    var $strUserName;
    /*
     * password that is to be used along with username
     */
    var $strPassword;
    /*
     * Sender Id to be used for submitting the message
     */
    var $strSender;
    /*
     * Message content that is to be transmitted
     */
    var $strMessage;
    /*
     * Mobile No is to be transmitted.
     */
    var $strMobile;
    /*
     * What type of the message that is to be sent
     * <ul>
     * <li>0:means plain text</li>
     * <li>1:means flash</li>
     * <li>2:means Unicode (Message content should be in Hex)</li>
     * <li>6:means Unicode Flash (Message content should be in Hex)</li>
     * </ul>
     */
    var $strMessageType;
    /*
     * Require DLR or not
     * <ul>
     * <li>0:means DLR is not Required</li>
     * <li>1:means DLR is Required</li>
     * </ul>
     */
    var $strDlr;

    private function smsUnicode($message) {
        $hex1 = '';

        if (function_exists('iconv')) {
            $latin = @iconv('UTF-8', 'ISO-8859-1', $message);
            if (strcmp($latin, $message)) {
                $arr = unpack('H*hex', @iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['hex']);
            }
            if ($hex1 == '') {
                $hex2 = '';
                $hex = '';
                for ($i = 0; $i < strlen($message); $i++) {
                    $hex = dechex(ord($message[$i]));
                    $len = strlen($hex);
                    $add = 4 - $len;
                    if ($len < 4) {
                        for ($j = 0; $j < $add; $j++) {
                            $hex = "0" . $hex;
                        }
                    }
                    $hex2.=$hex;
                }
                return $hex2;
            } else {
                return $hex1;
            }
        } else {
            print 'iconv Function Not Exists !';
        }
    }

//Constructor..
    public function SMS($host, $port, $username, $password, $sender, $message, $mobile, $msgtype, $dlr) {
        $this->host = $host;
        $this->port = $port;
        $this->strUserName = $username;
        $this->strPassword = $password;
        $this->strSender = $sender;
        $this->strMessage = $message; //URL Encode The Message..
        $this->strMobile = $mobile;
        $this->strMessageType = $msgtype;
        $this->strDlr = $dlr;
    }

    public function submit() {
        if ($this->strMessageType == "2" || $this->strMessageType == "6") {
//Call The Function Of String To HEX.
            $this->strMessage = $this->smsUnicode($this->strMessage);

            try {
                //Smpp http Url to send sms.
                $live_url = "http://" . $this->host . ":" . $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "&password=" . $this->strPassword . "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile . "&source=" . $this->strSender . "&message=" . $this->strMessage . "";
                $parse_url = file($live_url);
//                echo $parse_url[0];

                $grab = substr($parse_url[0], 0, 4);

                switch ($grab) {
                    case 1701:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-success'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Message, successfully sent!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
//                        redirect_to("send_bulk_sms_on.php");
                        break;
                    case 1702:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid URL!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1703:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Username and/or Password field!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1704:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Message type!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1705:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Message!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1706:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Phone number!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1707:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Sender!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    case 1708:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-info'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid value for Delivery Report!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1709:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>User Validation Failed!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1710:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Internal Error. Kindly, contact Anidrol Ghana!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1025:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-warning'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Insufficient credit in your account. Kindly, recharge!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
//                        redirect_to("sms_response_bal.php");
                        break;
                    case 1715:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-warning'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Response timeout!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                }
            } catch (Exception $e) {
                echo 'Message:' . $e->getMessage();
            }
        } else {
            $this->strMessage = urlencode($this->strMessage);

            try {
//Smpp http Url to send sms.
                $live_url = "http://" . $this->host . ":" . $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "&password=" . $this->strPassword . "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile . "&source=" . $this->strSender . "&message=" . $this->strMessage . "";
                $parse_url = file($live_url);
//                echo $parse_url[0];

                $grab = substr($parse_url[0], 0, 4);

                switch ($grab) {
                    case 1701:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-success'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Message, successfully sent!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
//                        redirect_to("send_bulk_sms_on.php");
                        break;
                    case 1702:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid URL!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1703:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Username and/or Password field!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1704:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Message type!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1705:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Message!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1706:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Phone number!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1707:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid Sender!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    case 1708:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-info'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Invalid value for Delivery Report!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1709:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>User Validation Failed!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1710:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-error'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Internal Error. Kindly, contact Anidrol Ghana!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                    case 1025:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-warning'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Insufficient credit in your account. Kindly, recharge!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
//                        redirect_to("sms_response_bal.php");
                        break;
                    case 1715:
                        echo "<div class='row'>";
                        echo "<div class='span12'>";
                        echo "<div class='alert alert-warning'>";
                        echo "<button type='button' class='close' data-dismiss='alert'></button>";
                        echo "<ul type='square'>";
                        echo "<li>Response timeout!</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        break;
                }
            } catch (Exception $e) {
                echo 'Message:' . $e->getMessage();
            }
        }
    }

}

//Call The Constructor.
//bulksms/bulksms
//$obj = new Sender("IP", "Port", "", "", "Tester", "العربية", "919990001245", "2", "1");
//$obj->Submit();
