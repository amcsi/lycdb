<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LyceeImageController extends AbstractActionController
{
    public function imageAction() {

        $sizeKey = $this->params()->fromRoute('sizeKey');

        $cid = $this->params()->fromRoute('cid');
        $externalImageName = str_replace('-', '_', strtolower($cid)) . '_l.jpg';
        $external = "http://lycee-tcg.com/card_list/images/card_images";

        $localImageName = "$cid.jpg";

        $imagePath = './data/generated_images/lycee';
        $localFileName = "$imagePath/original/$localImageName";
        if (!file_exists($localFileName)) {
            copy("$external/$externalImageName", $localFileName);
        }

        $params = array (
            'baseDir' => $imagePath,
            'filename' => $localImageName,
            'originalExtraDir' => 'original',
            'thumbExtraDir' => 'lol',
            'returnType'        => 2,
        );

        if (180 == $sizeKey) {
            $params['thumbExtraDir'] = '180';
            $params['width'] = '180';
            $params['height'] = null;
        }

        $vhm = $this->getServiceLocator()->get('ViewHelperManager');
        $thumbnailHelper = $vhm->get('thumbnail');
        $file = $thumbnailHelper($params);

        $this->httpCacheForDays(7);

        if ($ims = getenv('HTTP_IF_MODIFIED_SINCE')) {
            $filemtime = filemtime($file);
            if (strtotime($ims) < $filemtime) {
            }
            else {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }

        $getimagesize = getimagesize($file);
        if ($getimagesize) {
            /*
            header("Cache-Control: no-cache, must-revalidate"); 
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
             */
            header("Content-Type: $getimagesize[mime]");
            readfile($file);
        }
        else {
            header ("HTTP/1.1 404 Not Found");
            exit;
        }
        exit;

    }

    public function httpCacheForDays($n) {
        $seconds_to_cache = 60 * 60 * 24 * $n;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
    }
}
