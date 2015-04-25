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


class Barcode39 {
    static private $encoding = array(
        '101001101101', '110100101011', '101100101011', '110110010101',
        '101001101011', '110100110101', '101100110101', '101001011011',
        '110100101101', '101100101101', '110101001011', '101101001011',
        '110110100101', '101011001011', '110101100101', '101101100101',
        '101010011011', '110101001101', '101101001101', '101011001101',
        '110101010011', '101101010011', '110110101001', '101011010011',
        '110101101001', '101101101001', '101010110011', '110101011001',
        '101101011001', '101011011001', '110010101011', '100110101011',
        '110011010101', '100101101011', '110010110101', '100110110101',
        '100101011011', '110010101101', '100110101101', '100100100101',
        '100100101001', '100101001001', '101001001001', '100101101101');
    static public function getDigit($code){
        $table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-. $/+%*';
        $result = '';
        $intercharacter = '0';

        if (strpos($code, '*') !== false) return('');

        // Add Start and Stop charactere : *
        $code = strtoupper('*' . $code . '*');

        $len = strlen($code);
        for($i=0; $i<$len; $i++){
            $index = strpos($table, $code[$i]);
            if ($index === false) return('');
            if ($i > 0) $result .= $intercharacter;
            $result .= self::$encoding[ $index ];
        }
        return($result);
    }
}