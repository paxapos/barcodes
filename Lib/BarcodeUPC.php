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

class BarcodeUPC {

    static public function getDigit($code){
        if (strlen($code) < 12) {
            $code = '0' . $code;
        }
        return BarcodeEAN::getDigit($code, 'ean13');
    }

    static public function compute($code){
        if (strlen($code) < 12) {
            $code = '0' . $code;
        }
        return substr(BarcodeEAN::compute($code, 'ean13'), 1);
    }
}