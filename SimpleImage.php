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

        }

    }
?>
