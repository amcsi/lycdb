<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class LycdbMarkupToHtml extends AbstractHelper {

    public function __invoke($string) {
        $imgBase = $this->getView()->basePath() . '/img';
        $string = str_replace('[snow]', $this->getImgTag("$imgBase/snow.gif", '[snow]'), $string);
        $string = str_replace('[moon]', $this->getImgTag("$imgBase/moon.gif", '[moon]'), $string);
        $string = str_replace('[flower]', $this->getImgTag("$imgBase/flower.gif", '[flower]'), $string);
        $string = str_replace('[lightning]', $this->getImgTag("$imgBase/lightning.gif", '[lightning]'), $string);
        $string = str_replace('[sun]', $this->getImgTag("$imgBase/sun.gif", '[sun]'), $string);
        $string = str_replace('[star]', $this->getImgTag("$imgBase/star.gif", '[star]'), $string);
        $string = str_replace('[0]', $this->getImgTag("$imgBase/0.gif", '[0]'), $string);
        $string = str_replace('[tap]', $this->getImgTag("$imgBase/tap.gif", '[tap]'), $string);
        $string = str_replace('[on]', $this->getImgTag("$imgBase/spot-on.gif", '[on]'), $string);
        $string = str_replace('[off]', $this->getImgTag("$imgBase/spot-off.gif", '[off]'), $string);
        $string = preg_replace("@\[target\](.*?)\[/target\]@", '<span class="target">\1</span>', $string);
        $string = preg_replace("@\[color=(\w+)\](.*?)\[/color\]@", '<span style="color: \1;">\2</span>', $string);
        $string = nl2br($string);
        return $string;
    }

    public function getImgTag($src, $alt) {
        return "<img src=\"$src\" alt=\"$alt\">";
    }
}
