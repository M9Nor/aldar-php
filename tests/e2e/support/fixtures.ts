import { appShell } from './docker';

/** A real 8x8 JPEG produced by the app container's GD, so finfo and getimagesize accept it. */
export function jpegFixture(): Buffer {
  const base64 = appShell(`php -r '$i = imagecreatetruecolor(8, 8); ob_start(); imagejpeg($i); echo base64_encode(ob_get_clean());'`);
  return Buffer.from(base64, 'base64');
}
