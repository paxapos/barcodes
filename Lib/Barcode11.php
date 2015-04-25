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


class Barcode11 {
    static private $encoding = array(
        '101011', '1101011', '1001011', '1100101',
        '1011011', '1101101', '1001101', '1010011',
        '1101001', '110101', '101101');

    static public function getDigit($code){
        if (preg_match('`[^0-9\-]`', $code)) return '';
        $result = '';
        $intercharacter = '0';

        // start
        $result = '1011001' . $intercharacter;

        // digits
        $len = strlen($code);
        for($i=0; $i<$len; $i++){
            $index = $code[$i] == '-' ? 10 : intval($code[$i]);
            $result .= self::$encoding[ $index ] . $intercharacter;
        }

        // checksum
        $weightC    = 0;
        $weightSumC = 0;
        $weightK    = 1; // start at 1 because the right-most character is 'C' checksum
        $weightSumK = 0;
        for($i=$len-1; $i>-1; $i--){
            $weightC = $weightC == 10 ? 1 : $weightC + 1;
            $weightK = $weightK == 10 ? 1 : $weightK + 1;

            $index = $code[$i] == '-' ? 10 : intval($code[$i]);

            $weightSumC += $weightC * $index;
            $weightSumK += $weightK * $index;
        }

        $c = $weightSumC % 11;
        $weightSumK += $c;
        $k = $weightSumK % 11;

        $result .= self::$encoding[$c] . $intercharacter;

        if ($len >= 10){
            $result .= self::$encoding[$k] . $intercharacter;
        }

        // stop
        $result  .= '1011001';

        return($result);
    }
}
