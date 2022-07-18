<?php
namespace Winestop\Custom\Plugin;
class NewexpressionGenerator
{
        public function aftergetRandomExpression(
            \Snk\Captcha\Model\ExpressionGenerator $subject,
            $result
        ){
        	if ($result['result'] == 1){
        		$result['a'] = 10;
        		$result['b'] = 10;
        		$result['sign'] = '+';
        		$result['result'] = 20;
        	}
        	return $result;
        }
}