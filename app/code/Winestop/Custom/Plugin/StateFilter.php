<?php

namespace Winestop\Custom\Plugin;

class StateFilter
{
    protected $disallowed = [
        'American Samoa',
        'Armed Forces Africa',
        'Armed Forces Americas',
        'Armed Forces Canada',
        'Armed Forces Europe',
        'Armed Forces Middle East',
        'Armed Forces Pacific',
        'Federated States Of Micronesia',
        'Guam',
        'Marshall Islands',
        'Mississippi',
        'Northern Mariana Islands',
        'Palau',
        'Puerto Rico',
        'Utah',
        'Virgin Islands'
    ];

    public function afterToOptionArray($subject, $options)
    {
        $result = array_filter($options, function ($option) {
            if (isset($option['label']))
                return !in_array($option['label'], $this->disallowed);
            return true;
        });

        return $result;
    }
}

?>