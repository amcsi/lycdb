<?php
namespace Lycee;

class Lang {
    public static function getJapaneseBasicAbilityMap() {
        static $ret = array (
            'ダッシュ'                  => CHAR::DASH,
            'アグレッシブ'              => CHAR::AGGRESSIVE,
            'ステップ'                  => CHAR::STEP,
            'サイドステップ'            => CHAR::SIDE_STEP,
            'サイド・ステップ'          => CHAR::SIDE_STEP,
            'サイド･ステップ'           => CHAR::SIDE_STEP,
            'オーダーステップ'          => CHAR::ORDER_STEP,
            'オーダー・ステップ'        => CHAR::ORDER_STEP,
            'オーダー･ステップ'         => CHAR::ORDER_STEP,
            'ジャンプ'                  => CHAR::JUMP,
            'エスケープ'                => CHAR::ESCAPE,
            'サイドアタック'            => CHAR::SIDE_ATTACK,
            'タックストラッシュ'        => CHAR::TAX_TRASH,
            'タックス・トラッシュ'      => CHAR::TAX_TRASH,
            'タックス･トラッシュ'       => CHAR::TAX_TRASH,
            'タックスウェイクアップ'    => CHAR::TAX_WAKEUP,
            'タックス・ウェイクアップ'  => CHAR::TAX_WAKEUP,
            'タックス･ウェイクアップ'   => CHAR::TAX_WAKEUP,
            'サポーター'                => CHAR::SUPPORTER,
            'タッチ'                    => CHAR::TOUCH,
            'アタッカー'                => CHAR::ATTACKER,
            'ディフェンダー'            => CHAR::DEFENDER,
            'ボーナス'                  => CHAR::BONUS,
            'ペナルティ'                => CHAR::PENALTY,
            'デッキボーナス'            => CHAR::DECK_BONUS,
            'デッキ・ボーナス'          => CHAR::DECK_BONUS,
            'デッキ･ボーナス'           => CHAR::DECK_BONUS,
            'ブースト'                  => CHAR::BOOST,
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
            'Dash'                  => CHAR::DASH,
            'Aggressive'              => CHAR::AGGRESSIVE,
            'Step'                  => CHAR::STEP,
            'Side Step'            => CHAR::SIDE_STEP,
            'Order Step'          => CHAR::ORDER_STEP,
            'Jump'                  => CHAR::JUMP,
            'Escape'                => CHAR::ESCAPE,
            'Side Attack'            => CHAR::SIDE_ATTACK,
            'Tax Trash'      => CHAR::TAX_TRASH,
            'Tax Wakeup'  => CHAR::TAX_WAKEUP,
            'Supporter'                => CHAR::SUPPORTER,
            'Touch'                    => CHAR::TOUCH,
            'Attacker'                => CHAR::ATTACKER,
            'Defender'            => CHAR::DEFENDER,
            'Bonus'                  => CHAR::BONUS,
            'Penalty'                => CHAR::PENALTY,
            'Deck Bonus'          => CHAR::DECK_BONUS,
        );
        return $ret;
    }
}

