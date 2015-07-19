<?php
session_start();

$config = array(
	'width' => 200,
	'height' => 75,
	'random_bg_position' => true,
	'background' => true,
	'lang' => 'th',
	'line_size' => 2,
	'line_enabled' => true,
	'line_amount' => 3,
);
// ค้นหาภาพทั้งหมด
$images = @glob('images/*.jpg');
// ค้นหา font ทั้งหมด
$fonts = @glob('fonts/*.ttf');
// สุ่มภาพมา 1 ภาพ จาก ทั้งหมด
$i_rand = array_rand($images);
// สุ่ม font มา 1 font จากทั้งหมด
$f_rand = array_rand($fonts);
// สร้าง ภาพขึ้นมา
$image = imagecreatetruecolor($config['width'], $config['height']);
// นำเข้าภาพจากที่สุ่มมา
$image2 = @imagecreatefromjpeg($images[$i_rand]);

if ($config['line_enabled']) {
	// กำหนด สีของเส้น
	$line_color = imagecolorallocate($image, 240,240,240);
	// ทำให้เส้น smoooth ขึ้น
	imageantialias($image, true);
	// วาดเส้น
	for ($i = 0; $i < $config['line_amount'];$i++) {
		$y1 = rand() % 100;
		$y2 = rand() % 100;
		$x1 = 0;
		$x2 = 200;
		imageline($image, $x1, $y1 , $x2, $y2, $line_color);
		if ($config['line_size'] > 1) {
			for($l = 1; $l <= $config['line_size'];$l++) {
				imageline($image, $x1, $y1+$l , $x2, $y2+$l, $line_color);
			}
		}
	}	
}

switch ($config['lang']) {
	case 'th':
		$characters = array('ก','ข','ค','ฆ','ง','จ','ฉ','ช','ซ','ช','ฌ','ญ','ฎ','ฏ','ฐ','ฑ','ฒ','ณ','ต','ถ','ท','ธ','น','บ','ป','ผ','พ','ภ','ม','ย','ร','ล','ว','ส','ห','ฬ','อ','ฮ','๑','๒','๓','๔','๕','๖','๗','๘','๙');
		break;
	
	default:
		$characters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
		break;
}

$letters = $characters;
// กำหนดสีตัวอักษร
$text_color = imagecolorallocate($image, 255,255,255);
// เลือก font ที่สุ่มไว้แล้ว
$font = $fonts[$f_rand];
$cache = '';
// เขียนตัวอักษร ทีละตัว
for ($i = 0; $i< 6;$i++) {
	$letter = $letters[array_rand($letters)];
	$cache.= $letter;
	imagettftext($image, rand(28,38), rand(-20,20), 15+($i * 30), 50, $text_color, $font, $letter);
}
// นำ รหัส เข้า session
$_SESSION['captcha'] = strtolower($cache);

if ($config['background']) {
	$sx = 0; $sy = 0;
	// สำหรับภาพที่ขนาดไม่เท่ากับ captcha สุ่มตำแหน่ง ภาพพื้นหลัง
	if ($config['random_bg_position']) {
		$im = getimagesize($images[$i_rand]);
		$sx = rand(0, $im['0'] - $config['width']);
		$sy = rand(0, $im['1'] - $config['height']);
	}
	// ตั้งพื้นหลังให้กับ captcha
	imagecopymerge($image, $image2, 0, 0, rand(0,$sx), rand(0,$sy), $config['width'], $config['height'], 70);
}
ob_start();
	imagepng($image);
	imagedestroy($image);
	$contents = ob_get_contents();
ob_end_clean();
echo $contents;
