<?php
/**
 * @author      Oleh Kravets <oleh.kravets@snk.de>
 * @copyright   Copyright (c) 2021 schoene neue kinder GmbH  (https://www.snk.de)
 * @license     MIT
 */

namespace Snk\Captcha\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Captcha\Helper\Data;
use Magento\Captcha\Model\DefaultModel;
use Magento\Captcha\Model\ResourceModel\LogFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Session\SessionManagerInterface;
use Snk\Captcha\Model\Config\ExpressionType;
use Snk\Captcha\Model\ExpressionGenerator as Generator;
use Snk\Captcha\Helper\Config;

class MathCaptcha extends DefaultModel
{
    const TYPE = 'mathCaptcha';

    /**
     * @var Generator
     */
    private $expressionGenerator;

    /**
     * @var int
     */
    protected $dotNoiseLevel = 25;

    /**
     * @var int
     */
    protected $width = 350;

    /**
     * @var string
     */
    private $expressionString;

    /**
     * @var int
     */
    protected $lineNoiseLevel = 0;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        SessionManagerInterface $session,
        Data $captchaData,
        LogFactory $resLogFactory,
        Generator $expressionGenerator,
        $formId,
        Config $config,
        Random $randomMath = null
    ) {
        parent::__construct($session, $captchaData, $resLogFactory, $formId, $randomMath);
        $this->expressionGenerator = $expressionGenerator;
        $this->config = $config;
    }

    /**
     * @inheridoc
     */
    public function getBlockName()
    {
        return \Snk\Captcha\Block\Captcha\MathCaptcha::class;
    }

    /**
     * @inheridoc
     */
    protected function randomSize()
    {
        return Random::getRandomNumber(0, 2);
    }

    /**
     * @inheridoc
     */
    public function generateWord()
    {
        $this->expressionGenerator->setAllowedSigns($this->config->getAllowedMathSigns());
        $expression = $this->expressionGenerator->getRandomExpression();

        switch ($this->config->getExpressionType()) {
            case ExpressionType::TYPE_EQUATION:
                [$this->expressionString, $word] = $this->prepareEquationExpression($expression);
                break;
            case ExpressionType::TYPE_WORDS:
                [$this->expressionString, $word] = $this->prepareWordsExpression($expression);
                break;
            case ExpressionType::TYPE_NUMBERS:
            default:
                [$this->expressionString, $word] = $this->prepareNumberExpression($expression);
                break;
        }

        // kind of an easter egg :)
        if (time() % 100 === 1) {
            $this->expressionString = 'sin(π/2)+cos(2π)';
            return '2';
        }

        return (string) $word;
    }

    /**
     * @inheridoc
     */
    protected function generateImage($id, $word)
    {
        // use generated expression string
        parent::generateImage($id, $this->expressionString);
    }

    /**
     * Todo: extract?
     * @param array $expression
     * @return array
     */
    private function prepareNumberExpression($expression): array
    {
        return [
            sprintf(
                '%s%s%s=',
                $expression[Generator::KEY_FIRST],
                $expression[Generator::KEY_SIGN],
                $expression[Generator::KEY_SECOND]
            ),
            $expression[Generator::KEY_RESULT]
        ];
    }

    /**
     * * Todo: extract?
     * @param array $expression
     * @return array
     * @throws \Exception
     */
    private function prepareEquationExpression($expression)
    {
        $numbers = [
            $expression[Generator::KEY_FIRST],
            $expression[Generator::KEY_SECOND],
            $expression[Generator::KEY_RESULT]
        ];
        $formats = [
            'x %s %1 = %2',
            '%0 %s x = %2',
            '%0 %s %1 = x',
        ];
        $xIndex = random_int(0, 2);

        $text = str_replace(['%0', '%s', '%1', '%2'], $expression, $formats[$xIndex]);

        return [$text, $numbers[$xIndex]];
    }

    /**
     * * Todo: extract?
     * @param array $expression
     * @return array
     */
    private function prepareWordsExpression($expression)
    {
        $text = sprintf(
            '%s %s %s =',
            __("Num{$expression[ExpressionGenerator::KEY_FIRST]}"),
            $expression[ExpressionGenerator::KEY_SIGN],
            __("Num{$expression[ExpressionGenerator::KEY_SECOND]}")
        );

        return [$text, $expression[ExpressionGenerator::KEY_RESULT]];
    }
}
