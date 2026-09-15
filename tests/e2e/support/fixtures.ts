import { appShell } from './docker';

/** A real 8x8 JPEG produced by the app container's GD, so finfo and getimagesize accept it. */
export function jpegFixture(): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(8, 8); ob_start(); imagejpeg($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}

/** A solid-colour PNG of the given size, large enough for the CMS image rules (min 50x50). */
export function pngFixture(width = 120, height = 90): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(${width}, ${height}); imagefill($i, 0, 0, imagecolorallocate($i, 30, 120, 200)); ob_start(); imagepng($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}
