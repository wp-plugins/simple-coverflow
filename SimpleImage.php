<?php
    /*
    * File: SimpleImage.php
    * Author: Simon Jarvis
    * Copyright: 2006 Simon Jarvis
    * Date: 08/11/06
    * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
    * 
    * This program is free software; you can redistribute it and/or 
    * modify it under the terms of the GNU General Public License 
    * as published by the Free Software Foundation; either version 2 
    * of the License, or (at your option) any later version.
    * 
    * This program is distributed in the hope that it will be useful, 
    * but WITHOUT ANY WARRANTY; without even the implied warranty of 
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
    * GNU General Public License for more details: 
    * http://www.gnu.org/licenses/gpl.html
    *
    * 
    * modified by simon to use crop scale
    */

    if(!class_exists('SimpleImage')){
        class SimpleImage {

            var $image;
            var $image_type;
            var $cacheDir;
            var $cacheDirUrl;






            /**
            * round_corners
            *
            * Round the corners of an image. Transparency and anti-aliasing are supported.
            *
            * @version 0.1
            * @author Contributors at eXorithm
            * @link http://www.exorithm.com/algorithm/view/round_corners Listing at eXorithm
            * @link http://www.exorithm.com/algorithm/history/round_corners History at eXorithm
            * @license http://www.exorithm.com/home/show/license
            *
            * @param resource $image (GD image) 
            * @param number $radius Radius of the rounded corners.
            * @param string $color (hex color code) Color of the background.
            * @param mixed $transparency Level of transparency. 0 is no transparency, 127 is full transparency.
            * @return resource GD image
            */
            function roundCorners($radius=20,$color='ffffff',$transparency='127')
            {


                $image=$this->image;

                $width = imagesx($image);
                $height = imagesy($image);

                $image2 = imagecreatetruecolor($width, $height);
                imagecopy($image2, $image, 0, 0, 0, 0, $width, $height);

                imagesavealpha($image2, true);
                imagealphablending($image2, false);

                $full_color = $this->allocate_color($image2, $color, $transparency);

                // loop 4 times, for each corner...
                for ($left=0;$left<=1;$left++) {
                    for ($top=0;$top<=1;$top++) {

                        $start_x = $left * ($width-$radius);
                        $start_y = $top * ($height-$radius);
                        $end_x = $start_x+$radius;
                        $end_y = $start_y+$radius;

                        $radius_origin_x = $left * ($start_x-1) + (!$left) * $end_x;
                        $radius_origin_y = $top * ($start_y-1) + (!$top) * $end_y;

                        for ($x=$start_x;$x<$end_x;$x++) {
                            for ($y=$start_y;$y<$end_y;$y++) {
                                $dist = sqrt(pow($x-$radius_origin_x,2)+pow($y-$radius_origin_y,2));

                                if ($dist>($radius+1)) {
                                    imagesetpixel($image2, $x, $y, $full_color);
                                } else {
                                    if ($dist>$radius) {
                                        $pct = 1-($dist-$radius);
                                        $color2 = $this->antialias_pixel($image2, $x, $y, $full_color, $pct);
                                        imagesetpixel($image2, $x, $y, $color2);
                                    }
                                }
                            }
                        }

                    }
                }

                $this->image=$image2;
            }

            /**
            * allocate_color
            *
            * Helper function to allocate a color to an image. Color should be a 6-character hex string.
            *
            * @version 0.2
            * @author Contributors at eXorithm
            * @link http://www.exorithm.com/algorithm/view/allocate_color Listing at eXorithm
            * @link http://www.exorithm.com/algorithm/history/allocate_color History at eXorithm
            * @license http://www.exorithm.com/home/show/license
            *
            * @param resource $image (GD image) The image that will have the color allocated to it.
            * @param string $color (hex color code) The color to allocate to the image.
            * @param mixed $transparency The level of transparency from 0 to 127.
            * @return mixed
            */
            function allocate_color($image=null,$color='268597',$transparency='0')
            {
                if (preg_match('/[0-9ABCDEF]{6}/i', $color)==0) {
                    throw new Exception("Invalid color code.");
                }
                if ($transparency<0 || $transparency>127) {
                    throw new Exception("Invalid transparency.");
                }

                $r  = hexdec(substr($color, 0, 2));
                $g  = hexdec(substr($color, 2, 2));
                $b  = hexdec(substr($color, 4, 2));
                if ($transparency>127) $transparency = 127;

                if ($transparency<=0)
                    return imagecolorallocate($image, $r, $g, $b);
                else
                    return imagecolorallocatealpha($image, $r, $g, $b, $transparency);
            }

            /**
            * antialias_pixel
            *
            * Helper function to apply a certain weight of a certain color to a pixel in an image. The index of the resulting color is returned. 
            *
            * @version 0.1
            * @author Contributors at eXorithm
            * @link http://www.exorithm.com/algorithm/view/antialias_pixel Listing at eXorithm
            * @link http://www.exorithm.com/algorithm/history/antialias_pixel History at eXorithm
            * @license http://www.exorithm.com/home/show/license
            *
            * @param resource $image (GD image) The image containing the pixel.
            * @param number $x X-axis position of the pixel.
            * @param number $y Y-axis position of the pixel.
            * @param number $color The index of the color to be applied to the pixel.
            * @param number $weight Should be between 0 and 1,  higher being more of the original pixel color, and 0.5 being an even mixture.
            * @return mixed
            */
            function antialias_pixel($image=null,$x=0,$y=0,$color=0,$weight=0.5)
            {
                $c = imagecolorsforindex($image, $color);
                $r1 = $c['red'];
                $g1 = $c['green'];
                $b1 = $c['blue'];
                $t1 = $c['alpha'];

                $color2 = imagecolorat($image, $x, $y);
                $c = imagecolorsforindex($image, $color2);
                $r2 = $c['red'];
                $g2 = $c['green'];
                $b2 = $c['blue'];
                $t2 = $c['alpha'];

                $cweight = $weight+($t1/127)*(1-$weight)-($t2/127)*(1-$weight);

                $r = round($r2*$cweight + $r1*(1-$cweight));
                $g = round($g2*$cweight + $g1*(1-$cweight));
                $b = round($b2*$cweight + $b1*(1-$cweight));

                $t = round($t2*$weight + $t1*(1-$weight));

                return imagecolorallocatealpha($image, $r, $g, $b, $t);
            }


            function dropShadow($style='kant') {


                //style kan være kant, old eller normal
                //$dst er destination
                //$src er billedet

                $image=$this->image;
                //  list ($width, $height) = getImageSize($src); //the width and height of your image 

                #Get image width / height
                $width = ImageSX($image);
                $height = ImageSY($image);


                //Below I'm storing all 8 shadow images into memory.
                $shadowStyle = dirname(__FILE__)."/shadow/" . $style;
                $tl = imagecreatefromgif($shadowStyle . "/shadow_TL.gif");
                $t = imagecreatefromgif($shadowStyle . "/shadow_T.gif");
                $tr = imagecreatefromgif($shadowStyle . "/shadow_TR.gif");
                $r = imagecreatefromgif($shadowStyle . "/shadow_R.gif");
                $br = imagecreatefromgif($shadowStyle . "/shadow_BR.gif");
                $b = imagecreatefromgif($shadowStyle . "/shadow_B.gif");
                $bl = imagecreatefromgif($shadowStyle . "/shadow_BL.gif");
                $l = imagecreatefromgif($shadowStyle . "/shadow_L.gif");


                $w = imagesx($l); //Width of the left shadow image
                $h = imagesy($l); //Height of the left shadow image

                $canvasHeight = $height;// + (2 * $w);
                $canvasWidth = $width;// + (2 * $w);

                //create a blank canvas with these new dimensions
                $canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);

                // Putting your images together
                imagecopyresized($canvas, $t, 0, 0, 0, 0, $canvasWidth, $w, $h, $w);
                imagecopyresized($canvas, $l, 0, 0, 0, 0, $w, $canvasHeight, $w, $h);
                imagecopyresized($canvas, $b, 0, $canvasHeight - $w, 0, 0, $canvasWidth, $w, $h, $w);
                imagecopyresized($canvas, $r, $canvasWidth - $w, 0, 0, 0, $w, $canvasHeight, $w, $h);


                $w = imagesx($tl);
                $h = imagesy($tl);
                imagecopyresized($canvas, $tl, 0, 0, 0, 0, $w, $h, $w, $h);
                imagecopyresized($canvas, $bl, 0, $canvasHeight - $h, 0, 0, $w, $h, $w, $h);
                imagecopyresized($canvas, $br, $canvasWidth - $w, $canvasHeight - $h, 0, 0, $w, $h, $w, $h);
                imagecopyresized($canvas, $tr, $canvasWidth - $w, 0, 0, 0, $w, $h, $w, $h);


                $w = imagesx($l);
                imagecopyresampled($canvas, $image, $w, $w, 0, 0, imagesx($image)-(2 * $w), imagesy($image)-(2 * $w), imagesx($image), imagesy($image));




                $this->image=$canvas;


            }

            function load($filename) {
                $image_info = getimagesize($filename);
                $this->image_type = $image_info[2];
                if( $this->image_type == IMAGETYPE_JPEG ) {
                    $this->image = imagecreatefromjpeg($filename);
                } elseif( $this->image_type == IMAGETYPE_GIF ) {
                    $this->image = imagecreatefromgif($filename);
                } elseif( $this->image_type == IMAGETYPE_PNG ) {
                    $this->image = imagecreatefrompng($filename);
                }
            }
            function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
                if( $image_type == IMAGETYPE_JPEG ) {
                    imagejpeg($this->image,$filename,$compression);
                } elseif( $image_type == IMAGETYPE_GIF ) {
                    imagegif($this->image,$filename);         
                } elseif( $image_type == IMAGETYPE_PNG ) {
                    imagepng($this->image,$filename);
                }   
                if( $permissions != null) {
                    chmod($filename,$permissions);
                }
            }
            function output($image_type=IMAGETYPE_JPEG) {
                if( $image_type == IMAGETYPE_JPEG ) {
                    imagejpeg($this->image);
                } elseif( $image_type == IMAGETYPE_GIF ) {
                    imagegif($this->image);         
                } elseif( $image_type == IMAGETYPE_PNG ) {
                    imagepng($this->image);
                }   
            }
            function getWidth() {
                return imagesx($this->image);
            }
            function getHeight() {
                return imagesy($this->image);
            }
            function resizeToHeight($height) {
                $ratio = $height / $this->getHeight();
                $width = $this->getWidth() * $ratio;
                $this->resize($width,$height);
            }
            function resizeToWidth($width) {
                $ratio = $width / $this->getWidth();
                $height = $this->getheight() * $ratio;
                $this->resize($width,$height);
            }
            function scale($scale) {
                $width = $this->getWidth() * $scale/100;
                $height = $this->getheight() * $scale/100; 
                $this->resize($width,$height);
            }
            function resize($width,$height) {

                $new_height=$height;
                $new_width=$width;
                $image=$this->image;
                // Get original width and height
                $width = imagesx ($image);
                $height = imagesy ($image);
                $origin_x = 0;
                $origin_y = 0;

                // generate new w/h if not provided
                if ($new_width && !$new_height) {
                    $new_height = floor ($height * ($new_width / $width));
                } else if ($new_height && !$new_width) {
                        $new_width = floor ($width * ($new_height / $height));
                    }

                    // scale down and add borders
                    if ($zoom_crop == 3) {

                    $final_height = $height * ($new_width / $width);

                    if ($final_height > $new_height) {
                        $new_width = $width * ($new_height / $height);
                    } else {
                        $new_height = $final_height;
                    }

                }

                // create a new true color image
                $canvas = imagecreatetruecolor ($new_width, $new_height);
                imagealphablending ($canvas, false);

                // Create a new transparent color for image
                $color = imagecolorallocatealpha ($canvas, 0, 0, 0, 127);

                // Completely fill the background of the new image with allocated color.
                imagefill ($canvas, 0, 0, $color);

                // scale down and add borders
                if ($zoom_crop == 2) {

                    $final_height = $height * ($new_width / $width);

                    if ($final_height > $new_height) {

                        $origin_x = $new_width / 2;
                        $new_width = $width * ($new_height / $height);
                        $origin_x = round ($origin_x - ($new_width / 2));

                    } else {

                        $origin_y = $new_height / 2;
                        $new_height = $final_height;
                        $origin_y = round ($origin_y - ($new_height / 2));

                    }

                }

                // Restore transparency blending
                imagesavealpha ($canvas, true);


                $src_x = $src_y = 0;
                $src_w = $width;
                $src_h = $height;

                $cmp_x = $width / $new_width;
                $cmp_y = $height / $new_height;

                // calculate x or y coordinate and width or height of source
                if ($cmp_x > $cmp_y) {

                    $src_w = round ($width / $cmp_x * $cmp_y);
                    $src_x = round (($width - ($width / $cmp_x * $cmp_y)) / 2);

                } else if ($cmp_y > $cmp_x) {

                        $src_h = round ($height / $cmp_y * $cmp_x);
                        $src_y = round (($height - ($height / $cmp_y * $cmp_x)) / 2);

                    }

                    // positional cropping!
                    switch ($align) {
                    case 't':
                    case 'tl':
                    case 'lt':
                    case 'tr':
                    case 'rt':
                        $src_y = 0;
                        break;

                    case 'b':
                    case 'bl':
                    case 'lb':
                    case 'br':
                    case 'rb':
                        $src_y = $height - $src_h;
                        break;

                    case 'l':
                    case 'tl':
                    case 'lt':
                    case 'bl':
                    case 'lb':
                        $src_x = 0;
                        break;

                    case 'r':
                    case 'tr':
                    case 'rt':
                    case 'br':
                    case 'rb':
                        $src_x = $width - $new_width;
                        $src_x = $width - $src_w;
                        break;

                    default:
                        break;
                }

                imagecopyresampled ($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);



                //$new_image = imagecreatetruecolor($width, $height);
                // imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

                $this->image = $canvas;  


            }      


            function get($src,$width,$height,$shadowStyle=''){


                $cacheDir=$this->cacheDir;
                $caheUrlDir=$this->cacheDirUrl;
                $filename=$cacheDir.$shadowStyle.$width.'x'.$height.basename($src);

                if(file_exists($src) and is_file($src)){



                    if(!file_exists($filename)){

                        $this->load($src);
                        $this->resize($width,$height);

                        if($shadowStyle){
                            if($shadowStyle=='round'){
                                $this->roundCorners();
                            }
                            $this->dropShadow($shadowStyle);
                        }
                        $this->save($filename);

                    }

                    return $caheUrlDir.$shadowStyle.$width.'x'.$height.basename($src);
                }   

            }
        }

    }
?>
