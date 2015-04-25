<?php
/*
 *  BarCode Coder Library (BCC Library)
 *  BCCL Version 2.0
 *
 *  Porting : PHP
 *  Version : 2.0.3.1
 *
 *  Date    : 2013-01-06
 *  Author  : DEMONTE Jean-Baptiste <jbdemonte@gmail.com>
 *            HOUREZ Jonathan
 *
 *  Date    : 2013-12-24
 *  Leszek Boroch <borek@borek.net.pl>
 *  Modification in class Barcode128 to enable encoding extended characters
 *  (ASCII above 127). To use barcodes, keypad emulation must be enabled in scanner configuration 
 *  (tested with Motorola/Symbol LS2208).
 *
 *  Web site: http://barcode-coder.com/
 *  dual licence :  http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html
 *                  http://www.gnu.org/licenses/gpl.html
 */

class BarcodeI25 {
    static private $encoding = array('NNWWN', 'WNNNW', 'NWNNW', 'WWNNN', 'NNWNW', 'WNWNN', 'NWWNN', 'NNNWW', 'WNNWN','NWNWN');

    static public function compute($code, $crc, $type){
        if (! $crc) {
            if (strlen($code) % 2) $code = '0' . $code;
        } else {
            if ( ($type == 'int25') && (strlen($code) % 2 == 0) ) $code = '0' . $code;
            $odd = true;
            $sum = 0;
            for($i=strlen($code)-1; $i>-1; $i--){
                $v = intval($code[$i]);
                $sum += $odd ? 3 * $v : $v;
                $odd = ! $odd;
            }
            $code .= (string) ((10 - $sum % 10) % 10);
        }
        return($code);
    }

    static public function getDigit($code, $crc, $type){
        $code = self::compute($code, $crc, $type);
        if ($code == '') return($code);
        $result = '';

        if ($type == 'int25') { // Interleaved 2 of 5
            // start
            $result .= '1010';

            // digits + CRC
            $end = strlen($code) / 2;
            for($i=0; $i<$end; $i++){
                $c1 = $code[2*$i];
                $c2 = $code[2*$i+1];
                for($j=0; $j<5; $j++){
                    $result .= '1';
                    if (self::$encoding[$c1][$j] == 'W') $result .= '1';
                    $result .= '0';
                    if (self::$encoding[$c2][$j] == 'W') $result .= '0';
                }
            }
            // stop
            $result .= '1101';
        } else if ($type == 'std25') {
            // Standard 2 of 5 is a numeric-only barcode that has been in use a long time.
            // Unlike Interleaved 2 of 5, all of the information is encoded in the bars; the spaces are fixed width and are used only to separate the bars.
            // The code is self-checking and does not include a checksum.

            // start
            $result .= '11011010';

            // digits + CRC
            $end = strlen($code);
            for($i=0; $i<$end; $i++){
                $c = $code[$i];
                for($j=0; $j<5; $j++){
                    $result .= '1';
                    if (self::$encoding[$c][$j] == 'W') $result .= '11';
                    $result .= '0';
                }
            }
            // stop
            $result .= '11010110';
        }
        return($result);
    }
}
