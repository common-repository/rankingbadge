<?php
/**
 * omnimage.lib.php
 *
 * Function library for drawing roundedged rectangles
 *
 * @author Naden Badalgogtapeh <n.b@naden.de> <http://www.naden.de/blog>
 * @copyright 04.01.2009
 * @version 0.1
 *
 */

/**
 * imagefilledroundedrectangel
 *
 * Draws a filled rectangle with round corners
 *
 * @author Naden Badalgogtapeh <n.b@naden.de>
 *
 * @param  resource &$img pointer to image resource
 * @param  int $x x coordinate
 * @param  int $y y coordinate
 * @param  int $w width
 * @param  int $h height
 * @param  int $curve curve width for corners
 * @param  int $color background color
 *
 */
function imagefilledroundedrectangel( &$img, $x, $y, $w, $h, $curve, $color )
{
  // right bottom
  imagefilledarc( $img, $x + $w - $curve, $y + $h - $curve-1, $curve * 2, $curve * 2,  0, 90, $color, IMG_ARC_PIE );
#  imagefilledarc( $img, $x + $w - $curve, $y + $h - $curve, $curve * 2, $curve * 2,  0, 90, $color, IMG_ARC_PIE );
  // right top
  imagefilledarc( $img, $x + $w - $curve, $y + $curve, $curve * 2, $curve * 2,  270, 360, $color, IMG_ARC_PIE );
  // left bottom
  imagefilledarc( $img, $x + $curve, $y + $h - $curve-1, $curve * 2, $curve * 2, 90, 180, $color, IMG_ARC_PIE );
#  imagefilledarc( $img, $x + $curve, $y + $h - $curve, $curve * 2, $curve * 2, 90, 180, $color, IMG_ARC_PIE );
  // left top
  imagefilledarc( $img, $x + $curve,  $y + $curve, $curve * 2, $curve * 2, 180, 270, $color, IMG_ARC_PIE );
  // vertical
  imagefilledrectangle( $img, $x + $curve, $y, $x + $w - $curve, $y + $h, $color );
  // horizontal
  imagefilledrectangle( $img, $x, $y + $curve, $x + $w, $y + $h - $curve, $color );
}

/**
 * imageroundedrectangel
 *
 * Draws a rectangle with round corners and no background
 *
 * @author Naden Badalgogtapeh <n.b@naden.de>
 *
 * @param  resource &$img pointer to image resource
 * @param  int $x x coordinate
 * @param  int $y y coordinate
 * @param  int $w width
 * @param  int $h height
 * @param  int $curve curve width for corners
 * @param  int $color border color
 * @param  int $border border width in pixel
 *
 */
function imageroundedrectangel( &$img, $x, $y, $w, $h, $curve, $color, $border = 1 )
{
  for( $k=0; $k<$border; $k++ )
  {
    // left top
    imagearc( $img, $x + $curve, $y + $curve, ($curve-$k) * 2, ($curve-$k) * 2, 180, 270, $color );
    // right bottom
    imagearc( $img, $x + $w - $curve, $y + $h - $curve, ($curve-$k) * 2, ($curve-$k) * 2,  0, 90, $color );
    // right top
    imagearc( $img, $x + $w - $curve, $y + $curve, ($curve-$k) * 2, ($curve-$k) * 2, 270, 360, $color );
    // left bottom
    imagearc( $img, $x + $curve, $y + $h - $curve, ($curve-$k) * 2, ($curve-$k) * 2, 90, 180, $color );
  }

  // left line
  imagefilledrectangle( $img, $x, $y + $curve, $x+$border-1, $y + $h - $curve, $color );
  // right line
  imagefilledrectangle( $img, $x + $w - $border, $y + $curve, $x + $w, $y + $h - $curve, $color );
  // top line
  imagefilledrectangle( $img, $x + $curve, $y, $x + $w - $curve, $y + $border-1, $color );
  // bottom line
  imagefilledrectangle( $img, $x + $curve, $y + $h - $border, $x + $w - $curve, $y + $h, $color );
}

/**
 * imagefilledroundedrectangelborder
 *
 * Draws a rectangle with round corners and border
 *
 * @author Naden Badalgogtapeh <n.b@naden.de>
 *
 * @param  resource &$img pointer to image resource
 * @param  int $x x coordinate
 * @param  int $y y coordinate
 * @param  int $w width
 * @param  int $h height
 * @param  int $curve curve width for corners
 * @param  int $color1 background color
 * @param  int $color2 border color
 * @param  int $border border width in pixel
 *
 */
function imagefilledroundedrectangelborder( &$img, $x, $y, $w, $h, $curve, $color1, $color2, $border = 1 )
{
  imagefilledroundedrectangel( $img, $x, $y, $w, $h, $curve, $color1 );
  imageroundedrectangel( $img, $x, $y, $w, $h, $curve, $color2, $border );
}

?>