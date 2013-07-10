<?php
namespace Lycee;

class Lang {
    public static function getJapaneseBasicAbilityMap() {
        static $ret = array (
            'ダッシュ'                  => Char::DASH,
            'アグレッシブ'              => Char::AGGRESSIVE,
            'ステップ'                  => Char::STEP,
            'サイドステップ'            => Char::SIDE_STEP,
            'サイド・ステップ'          => Char::SIDE_STEP,
            'サイド･ステップ'           => Char::SIDE_STEP,
            'オーダーステップ'          => Char::ORDER_STEP,
            'オーダー・ステップ'        => Char::ORDER_STEP,
            'オーダー･ステップ'         => Char::ORDER_STEP,
            'ジャンプ'                  => Char::JUMP,
            'エスケープ'                => Char::ESCAPE,
            'サイドアタック'            => Char::SIDE_ATTACK,
            'タックストラッシュ'        => Char::TAX_TRASH,
            'タックス・トラッシュ'      => Char::TAX_TRASH,
            'タックス･トラッシュ'       => Char::TAX_TRASH,
            'タックスウェイクアップ'    => Char::TAX_WAKEUP,
            'タックス・ウェイクアップ'  => Char::TAX_WAKEUP,
            'タックス･ウェイクアップ'   => Char::TAX_WAKEUP,
            'サポーター'                => Char::SUPPORTER,
            'タッチ'                    => Char::TOUCH,
            'アタッカー'                => Char::ATTACKER,
            'ディフェンダー'            => Char::DEFENDER,
            'ボーナス'                  => Char::BONUS,
            'ペナルティ'                => Char::PENALTY,
            'デッキボーナス'            => Char::DECK_BONUS,
            'デッキ・ボーナス'          => Char::DECK_BONUS,
            'デッキ･ボーナス'           => Char::DECK_BONUS,
            'ブースト'                  => Char::BOOST,
        );
        return $ret;
    }

    public static function getJapaneseBasicAbilityFlippedMap() {
        static $ret;
        if (!$ret) {
            $ret = array_flip(self::getJapaneseBasicAbilityMap());
        }
        return $ret;
    }

    public static function getEnglishBasicAbilityMap() {
        static $ret = array (
            'Dash'                  => Char::DASH,
            'Aggressive'              => Char::AGGRESSIVE,
            'Step'                  => Char::STEP,
            'Side Step'            => Char::SIDE_STEP,
            'Order Step'          => Char::ORDER_STEP,
            'Jump'                  => Char::JUMP,
            'Escape'                => Char::ESCAPE,
            'Side Attack'            => Char::SIDE_ATTACK,
            'Tax Trash'      => Char::TAX_TRASH,
            'Tax Wakeup'  => Char::TAX_WAKEUP,
            'Supporter'                => Char::SUPPORTER,
            'Touch'                    => Char::TOUCH,
            'Attacker'                => Char::ATTACKER,
            'Defender'            => Char::DEFENDER,
            'Bonus'                  => Char::BONUS,
            'Penalty'                => Char::PENALTY,
            'Deck Bonus'          => Char::DECK_BONUS,
        );
        return $ret;
    }
    
    public static function en2JpMap($name = null) {
        static $map;
        if (!$map) {
            $map = array ();
            $en2CodeMap = self::getEnglishBasicAbilityMap();
            $code2JpMap = self::getJapaneseBasicAbilityFlippedMap();
            foreach ($en2CodeMap as $en => $code) {
                $map[$en] = $code2JpMap[$code];
            }
        }
        if ($name) {
            return $map[$name];
        }
        return $map;
    }

    /*
    public static function normalizeJapaneseBasicAbilityName($name = null) {
        static $map;
        if (!$map) {
            $map = array ();
            $jbam = self::getJapaneseBasicAbilityMap();
            $ebamFlipped = array_flip(self::getEnglishBasicAbilityMap);
            foreach ($jbam as $japaneseText => $code) {
                $map[$japaneseText] = $ebamFlipped[$code];
            }
        }
        if ($name) {
            if (isset($map[$name])) {
                $enName = $map[$name];
                return Char::enBasicAbilityToMarkup($enName);
            }
            else {
                return false;
            }
        }
        return $map;
    }
     */
}
