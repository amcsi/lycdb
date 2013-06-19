<?php
namespace Lycee;
/**
 * Class for importing files from http://lycee-tcg.com/
 *
 * S_L - which filter menu should be open by default
 *      1: sets
 * page_out - How many results to display per page
 * page_list - Style to show results.
 *      1: thumbnail view
 *      2: list view (with all needed details except image)
 * 
 **/
use \Zend\Dom\Query;

class LyceeImporter {

    public $baseUrl = 'http://lycee-tcg.com/card_list';
    public $convertToUtf8 = true;
    public $websiteVersionTag = "website-version-1";

    /**
     * \Lycee\Zend\CacheHelper
     * 
     * @var mixed
     * @access protected
     */
    protected $_cache;
    protected $_serviceManager;

    public function __construct() {
    }

    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager) {
        $this->_serviceManager = $serviceManager;
    }

    public function getCache() {
        if (!$this->_cache) {
            $zendCache = $this->_serviceManager->get('Lycee\Cache');
            $cacheHelper = new Zend\CacheHelper($zendCache);
            $this->_cache = $cacheHelper;
        }
        return $this->_cache;
    }

    public function import() {
        $sets = $this->getSets();
        $this->importSetByArray($sets[0]);
    }

    public function importSetByArray($arr) {
        $parsedUrl = parse_url($arr['page']);
        parse_str($parsedUrl['query'], $qs);
        $qs['page_out'] = 500;
        $qs['page_list'] = 2;
        $options = array ();
        $options['lifetime'] = 60 * 60 * 24 * 265 * 5; // 5 years.
        $options['cache_tags'] = array ($this->websiteVersionTag);
        $html = $this->request($parsedUrl['path'], $qs, $options);

        $domQuery = new \Zend\Dom\Query($html);
        $selector = '#card_list_main div.m_15 > *';
        $selectEls = $domQuery->execute($selector);

        $tableArray = array ();

        $cards = array ();

        /**
         * Cards in the HTML are usually 4 tables separated by a br. 2 brs mark the end. 
         */
        foreach ($selectEls as $selectEl) {
            if ('table' == $selectEl->tagName) {
                $tableArray[] = $selectEl;
            }
            else if ('br' == $selectEl->tagName) {
                if ($tableArray) {
                    $card = $this->getCardByTablesList2($tableArray);
                    $cards[] = $card;
                }
                $tableArray = array ();
            }
        }
        var_dump(count($cards));
    }

    public function getCardByTablesList2(array $tableArray) {
        /**
         * Card id, type, name, elements, ex
         **/
        $firstCells = $tableArray[0]->getElementsByTagName('td');
        $cidText = trim(strip_tags($firstCells->item(0)->textContent));
        $cardTypeText = trim($firstCells->item(1)->textContent, " \t\n\r\0\x0B　");
        $name = trim($firstCells->item(2)->textContent);
        $pattern = '@<img src="([^\"]*)"@';
        $elementArr = $this->countElementsByDomElement($firstCells->item(3));
        $exText = trim($firstCells->item(4)->textContent);
        preg_match('@EX　(\d+)@', $exText, $matches2);
        $ex = $matches2[1];

        $card = Card::newCardByTypeText($cardTypeText);
        $isChar = $card instanceof Char;
        $card->setCidText($cidText);
        $card->setJpName($name);
        $card->setElementByJapaneseArray($elementArr);
        $card->ex = (int) $ex;

        /**
         * Card cost, position, ap, dp, sp, gender, rarity
         **/
        $secondCells = $tableArray[1]->getElementsByTagName('td');
        $costElementArr = $this->countElementsByDomElement($secondCells->item(0));
        $card->setCostByJapaneseArray($costElementArr);
        if ($isChar) {
            $positionImgs = $secondCells->item(1)->getElementsByTagName('img');
            $flags = 0;
            for ($i = 0; $i < 6; $i++) {
                $img = $positionImgs->item($i);
                $hasPosition = false !== strpos($img->getAttribute('href'), 'b.gif');
                if ($hasPosition) {
                    $flags |= (1 << $i);
                }
            }
            if ($flags) {
                $card->setSpotFlags($flags);
            }
            $ap = str_replace('AP　', '', $secondCells->item(2 + 6)->textContent);
            $dp = str_replace('DP　', '', $secondCells->item(3 + 6)->textContent);
            $sp = str_replace('SP　', '', $secondCells->item(4 + 6)->textContent);
            $gender = str_replace('性別　', '', $secondCells->item(5 + 6)->textContent);
            $card->setGenderByText($gender);
            $card->setStat(Char::STAT_AP, $ap);
            $card->setStat(Char::STAT_DP, $dp);
            $card->setStat(Char::STAT_SP, $sp);
        }
        $rarity = trim(str_replace('ﾚｱﾘﾃｨ　', '', $secondCells->item(6 + 6)->textContent));
        $card->rarity = $rarity;

        var_dump($card);
    }

    /**
     * Counts the amount of elements in a dom element by its images.
     * The japanese element names are used as the keys.
     * 
     * @param mixed $html 
     * @access public
     * @return void
     */
    public function countElementsByDomElement(\DomElement $el) {
        $elementArr = array ();
        $elementImgEls = $el->getElementsByTagName('img');
        foreach ($elementImgEls as $el) {
            $alt = $el->getAttribute('alt');
            if (!isset($elementArr[$alt])) {
                $elementArr[$alt] = 1;
            }
            else {
                $elementArr[$alt]++;
            }
        }
        return $elementArr;
    }

    /**
     * Counts the amount of elements in a partial html by its images
     * 
     * @param mixed $html 
     * @access public
     * @return void
     */
    public function countElementsByHtml($html) {

    }

    public function getSets() {
        $key = 'setsArray';
        $cache = $this->getCache();
        $sets = $cache->getCachedResult($key);
        if (!$sets) {
            $indexHtml = $this->getIndexHtmlWithSetsOpen();
            $domQuery = new \Zend\Dom\Query();
            $domQuery->setDocumentHtml($indexHtml, 'utf-8');
            $setEls = $domQuery->execute('#card_list_main div.m_14a div.m_14b_y div.m_14e a');
            $ret = array ();
            foreach ($setEls as $el) {
                $set['page'] = $el->getAttribute('href');
                $text = trim($el->textContent);
                $pattern = '@^(.*)（(\d+)）$@'; // note the japanese parentheses characters
                preg_match($pattern, $text, $matches);
                $set['name'] = $matches[1];
                $set['count'] = $matches[2];
                $ret[] = $set;
            }
            $cache->cacheResult($sets, $key);
        }
        return $ret;
    }

    /**
     * Requests the index page. This is where we should find the card list.
     * Should be cached per day.
     * 
     * @access public
     * @return void
     */
    public function getIndexHtmlWithSetsOpen() {
        $page = 'index.cgi';
        $params = array ('S_L' => 1);
        $indexHtml = $this->request($page, $params);
        return $indexHtml;
    }

    public function request($url, $params = array (), $options = array ()) {
        $fullUrl = "$this->baseUrl/$url";
        return $this->requestFullUrl($fullUrl, $params, $options);
    }

    public function requestFullUrl($url, $params = array (), $options = array ()) {
        $cache = $this->getCache();

        $useCache = !isset($options['use_cache']) || empty($options['use_cache']); // use cache by default.
        if (!$useCache || !($result = $cache->getCachedResult($url, $params, $options))) {
            $args = (array) $params;
            $result = $this->_requestFullUrl($url, $args, $options);
            if ($result) {
                $cache->cacheResult($result, $url, $params, $options);
            }
        }
        $convertToUtf8 = $this->convertToUtf8;
        if (isset($options['convertToUtf8'])) {
            $convertToUtf8 = $options['convertToUtf8'];
        }
        if ($convertToUtf8) {
            $result = mb_convert_encoding($result, 'utf-8', array ('EUC_JP'));
            $result = str_replace('charset=EUC-JP', 'charset=UTF-8', $result);
        }
        return $result;
        
    }

    protected function _requestFullUrl($url, $params = array (), $options = array ()) {
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
		$result = file_get_contents($url);
		return $result;
    }
}
