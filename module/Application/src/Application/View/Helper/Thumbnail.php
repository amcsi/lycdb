<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use \Zebra_Image;

class Thumbnail extends AbstractHelper {

    public function __construct() {
        require 'vendor/zebra/zebra/src/Zebra/Image.php';
    }

    public function __invoke($params) {
        return $this->thumbnail($params);
    }

	/**
	 * Templatelite function arra, hogy automatikusan átméretezett thumbnailt készítsen,
	 * ha még nem készült, és visszaadja az elérhetőségét.
	 * 
	 * Paraméterek:
	 * baseDir - A projekt rootja utántól lévő elérése a könyvtárnak, amiben a thumbnail
	 *	könyvtár van
	 * filename - Az eredeti kép fájlneve
	 * width - A cél szélesség
	 * height - A cél magasság
	 * originalExtraDir - (Opcionális) Ha nem a baseDir-en belül van az eredeti kép, a
	 *	baseDir-hez képest ebben az alkönyvtárból szedje az eredeti képet
	 * thumbExtraDir - A baseDir-hez képest mi legyen a thumbnail könyvtár neve.
	 *	Alapértelmezetten "<szélesség>x<magasság>/"
	 * resizeType - Cropolás/átméreteés terén milyen módszerrel dolgozzon?
	 *	Alapértelmezetten croppol, és középreigazít, hogy ne maradjon üres hely. Lásd
	 *	a Zebra_Image class-t.
	 * thumbPrefix - (Opcionális) Prefix, amit a thumbnail kép fájlneve kapjon.
	 * returnType - (Opcionális) Mit adjon vissza. 
	 *	0 esetén komplett img tag-et.
	 *	1 esetén csupán a thumbnail kép url-jét.
	 *	2 esetén a thumbnail kép elérését a fájlrendszerben.
	 *	Alapértelmezetten 0, vagyis a komplett html tag-et.
	 * escape - (Optionális) escape-elje az outputot valahogyan.
	 *	"html" esetén htmlspecialchars
	 *	"javascript" esetén javascript escape-el
	 * <minden más> - (Opcionálius) Az img tag attribútumai.
	 *	A html escape-elés megtörténik automatikusan bennük.
	 *
	 * @return string Az img tag
	 */
	public function thumbnail($params, &$tpl = null) {
		$er = error_reporting(error_reporting() | E_NOTICE);
		if (!($baseDir = $params['baseDir'])) {
			throw new LogicException ("'baseDir' must be given.");
		}
		$baseDir = rtrim($baseDir, '/') . '/';
		$originalExtraDir = !empty($params['originalExtraDir']) ?
			rtrim($params['originalExtraDir'], '/') . '/' :
			'';
		$filename = $params['filename'];
		$width = $params['width'];
		$height = $params['height'];
		$thumbExtraDir = !empty($params['thumbExtraDir']) ? 
			rtrim($params['thumbExtraDir'], '/') . '/':
			"{$width}x{$height}/";

		$newFilename = $filename;
		if (!empty($params['thumbPrefix'])) {
			$newFilename = $params['thumbPrefix'] . $filename;
		}
		
		$imgFilename = $baseDir . $originalExtraDir .
			$filename;
		$thumbDir = $baseDir . $thumbExtraDir;
		$thumbFilename = $thumbDir . $newFilename;

        $src = /* url */ $baseDir . $thumbExtraDir .
			$newFilename;

		$dontResize = file_exists($thumbFilename);
        $imagePathScheme = parse_url ($imgFilename, PHP_URL_SCHEME);
		if ($dontResize && !$imagePathScheme) {
			$imgModTime = filemtime($imgFilename);
			$thumbModTime = filemtime($thumbFilename);
			$dontResize = $imgModTime <= $thumbModTime;
		}

		if ($dontResize) {

		}
		else {
			if (!file_exists($thumbDir)) {
				mkdir($thumbDir);
				chmod($thumbDir, 0777);
			}

			$zi = new Zebra_Image;
			$zi->jpeg_quality = 90;
			$zi->chmod_value = 0666;
			$zi->source_path = $imgFilename;
			$zi->target_path = $thumbFilename;

			$resizeType = isset($params['resizeType']) ? $params['resizeType'] :
				ZEBRA_IMAGE_CROP_CENTER;

			$success = $zi->resize($width, $height, $resizeType);
			if (!$success) {
				switch ($zi->error) {
				case 1:
					break;
				default:
					trigger_error("Zebra_Image resize failed. Error code: " .
						$zi->error, E_USER_WARNING);
					break;
				}
				if (!empty($params['missingFilename'])) {
					$src = $this->_engine->url['www'] . $baseDir . $thumbExtraDir .
						$params['missingFilename'];
				}
			}
		}

		$escape = !empty($params['escape']) ? $params['escape'] : '';

		$returnType = !empty($params['returnType']) ? $params['returnType'] : 0;
		if (0 == $returnType) {
			/**
			 * Ami végül marad a $param-okból, azokból lesznek az attribútumok.
			 **/
			unset ($params['baseDir'], $params['filename'], $params['originalExtraDir'],
				$params['thumbExtraDir'], $params['thumbPrefix']);
			unset($params['resizeType'], $params['returnType'], $params['escape']);

			$params['alt'] = !empty($params['alt']) ? $params['alt'] : 'image';
			$params['src'] = $src;

			$tag = '<img';
			foreach ($params as $key => $value) {
				$tag .= " $key=\"" . htmlspecialchars($value) . '"';
			}
			if (empty($this->html5)) {
				$tag .= ' /';
			}
			$tag .= '>';
			$ret = $tag;
		}
		else if (1 == $returnType) {
			$ret = $src;
		}
		else if (2 == $returnType) {
			$ret = $thumbFilename;
		}

		switch ($escape) {
		case 'html':
			$ret = htmlspecialchars($ret, ENT_QUOTES, 'utf-8');
			break;
		case 'javascript':
			$ret = strtr($ret, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
			break;
		default:
			break;
		}
		
		error_reporting($er);
		return $ret;
	}

}
