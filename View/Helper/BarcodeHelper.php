<?php

App::uses('Luhn', 'Barcodes.Lib');
App::uses('AppHelper', 'View/Helper');
App::uses('Barcode', 'Barcodes.Lib');

class BarcodeHelper extends AppHelper {

	public $fontSize = 5;   // GD1 in px ; GD2 in point
	public $marge    = 0;   // between barcode and hri in pixel
	public $x        = 500;  // barcode center
	public $y        = 60;  // barcode center
	public $height   = 50;   // barcode height in 1D ; module size in 2D
	public $width    = 2.5;    // barcode height in 1D ; not use in 2D
	public $angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisatio		  


	public $imageWidth  = 1000;
	public $imageHeigth = 180;


/**
 * Other helpers
 *
 * @var array
 */
	public $helpers = array('Risto.PxHtml');


	/**
	*
	*	Luhn Modulo 10 checker
	*
	**/
	public $Luhn;


	public function __construct(View $View, $settings = array()) {
		$this->Luhn = new Luhn;
		parent::__construct($View, $settings);
	}



	public function setCodeData ( $arrBarCode ) {
			$barcode = implode('', $arrBarCode);
			$digito = $this->Luhn->calculate( $barcode );
			return $barcode.$digito;
	}



	/**
	* 	standard 2 of 5 (std25)
	* 	interleaved 2 of 5 (int25)
	* 	ean 8 (ean8)
	* 	ean 13 (ean13)
	* 	upc (upc)
	* 	code 11 (code11)
	* 	code 39 (code39)
	* 	code 93 (code93)
	* 	code 128 (code128)
	* 	codabar (codabar)
	* 	msi (msi)
	* 	datamatrix (datamatrix)
	**/
	public function dataImage ( $type, $code, $ops ) {
		
		 if ( is_array($code )) {
		 	$code = $this->setCodeData( $code );
		 }

	
		  
		  // -------------------------------------------------- //
		  //            ALLOCATE GD RESSOURCE
		  // -------------------------------------------------- //
		  $im     = imagecreatetruecolor($this->imageWidth, $this->imageHeigth);
		  $black  = ImageColorAllocate($im,0x00,0x00,0x00);
		  $white  = ImageColorAllocate($im,0xff,0xff,0xff);
		  $red    = ImageColorAllocate($im,0xff,0x00,0x00);
		  $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
		  imagefilledrectangle($im, 0, 0, $this->imageWidth, $this->imageHeigth, $white);

		    // -------------------------------------------------- //
		  //                      BARCODE
		  // -------------------------------------------------- //
		  $data = Barcode::gd($im, $black, $this->x, $this->y, $this->angle, $type, array('code'=>$code), $this->width, $this->height);

  			$code = implode(' ',str_split($code));

  			$fw = imagefontwidth($this->fontSize);     // width of a character
			$l = strlen($code);          // number of characters
			$tw = $l * $fw;              // text width
			$iw = imagesx($im);          // image width

			$xpos = ($iw - $tw)/2;
  			imagestring($im, $this->fontSize, $xpos, $this->y + 60, $code , $black);


		  // -------------------------------------------------- //
		  //                        HRI
		  // -------------------------------------------------- //
		  if ( isset($this->font) ){
		    $box = imagettfbbox($this->fontSize, 0, $this->font, $data['hri']);
		    $len = $box[2] - $box[0];
		    Barcode::rotate(-$len / 2, ($data['height'] / 2) + $this->fontSize + $this->marge, $this->angle, $xt, $yt);
		    imagettftext($im, $this->fontSize, $this->angle, $this->x + $xt, $this->y + $yt, $blue, $this->font, $data['hri']);
		  }


		  // -------------------------------------------------- //
		  //                    MIDDLE AXE
		  // -------------------------------------------------- //
		  //imageline($im, $this->x, 0, $this->x, 250, $red);
		  //imageline($im, 0, $this->y, 250, $this->y, $red);
		  
		  // -------------------------------------------------- //
		  //                  BARCODE BOUNDARIES
		  // -------------------------------------------------- //
		  for($i=1; $i<5; $i++){
		   // $this->__drawCross($im, $blue, $data['p'.$i]['x'], $data['p'.$i]['y']);
		  }
		  
		  // -------------------------------------------------- //
		  //                    GENERATE
		  // -------------------------------------------------- //
		  // header('Content-type: image/gif');
		  $temp = tmpfile();
		  $metaDatas = stream_get_meta_data($temp);
		  $tmpFilename = $metaDatas['uri'];
		  imagepng($im, $tmpFilename);

		  $fcon = file_get_contents( $tmpFilename );
		  imagedestroy($im);

		  $media = array(
		  	'type' => 'jpg',
		  	'file' =>  $fcon
		  	);


		  $pxiimg = $this->PxHtml->imageData( $media, $ops );
		
		  return $pxiimg;
	}



	// -------------------------------------------------- //
	  //                    USEFUL
	  // -------------------------------------------------- //
	  
	private function __drawCross($im, $color, $x, $y){
	    imageline($im, $x - 10, $y, $x + 10, $y, $color);
	    imageline($im, $x, $y- 10, $x, $y + 10, $color);
	  }


}
