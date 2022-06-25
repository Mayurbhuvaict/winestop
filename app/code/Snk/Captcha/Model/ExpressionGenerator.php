<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */

namespace Snk\Captcha\Model;

use Magento\Framework\Exception\LocalizedException;

class ExpressionGenerator
{
    const SIGN_SUM = '+';
    const SIGN_SUB = '−';
    const SIGN_MUL = '×';
    const SIGN_DIV = '÷';

    const AVAILABLE_SIGNS = [
        self::SIGN_SUM,
        self::SIGN_MUL,
        self::SIGN_SUB,
        self::SIGN_DIV
    ];

    const KEY_FIRST = 'a';
    const KEY_SECOND = 'b';
    const KEY_SIGN = 'sign';
    const KEY_RESULT = 'result';

    /**
     * @var string[]
     */
    private $allowedSigns = self::AVAILABLE_SIGNS;

    /**
     * @var int
     */
    private $maxNumberSize = 10;

    /**
     * @param array $allowedSigns
     * @return void
     */
    public function setAllowedSigns(array $allowedSigns)
    {
        $this->allowedSigns = array_intersect($allowedSigns, self::AVAILABLE_SIGNS);
    }

    /**
     * @param int $size
     * @return void
     */
    public function setMaxNumberSize(int $size)
    {
        $this->maxNumberSize = $size;
    }

    /**
     * @return array
     * [
     *   int    $a,
     *   string $operationSign,
     *   int    $b,
     *   int    $result
     * ]
     * @throws LocalizedException
     */
    public function getRandomExpression()
    {
        $sign = $this->allowedSigns[array_rand($this->allowedSigns)];

        $a = random_int(1, $this->maxNumberSize);
        $b = random_int(1, $this->maxNumberSize);

        switch ($sign) {
            case self::SIGN_SUM:
                $result = $this->buildExpressionArray($a, self::SIGN_SUM, $b, $a + $b);
                break;
            case self::SIGN_MUL:
                $result = $this->buildExpressionArray($a, self::SIGN_MUL, $b, $a * $b);
                break;
            case self::SIGN_SUB:
                // swap values so bigger comes first
                if ($a < $b) {
                    $t = $a;
                    $a = $b;
                    $b = $t;
                }
                $result = $this->buildExpressionArray($a, self::SIGN_SUB, $b, $a - $b);
                break;
            case self::SIGN_DIV:
                $result = $this->buildExpressionArray($a * $b, self::SIGN_DIV, $a, $b);
                break;
            default:
                throw new LocalizedException(__('Unknown operation: %s', $sign));
        }

        return $result;
    }

    /**
     * @param int $a
     * @param int $b
     * @param string $sign
     * @param int $result
     * @return array
     */
    private function buildExpressionArray($a, $sign, $b, $result)
    {
        return [
            self::KEY_FIRST  => $a,
            self::KEY_SIGN   => $sign,
            self::KEY_SECOND => $b,
            self::KEY_RESULT => $result
        ];
    }
}
