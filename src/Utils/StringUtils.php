<?php

namespace App\Utils;

class StringUtils
{
    public static function cleanup(string $string): string
    {
        return
            preg_replace(
                '!\s+!',
                ' ',
                    str_replace("\n", '',
                        str_replace(
                            '<br/>',
                            '',
                            mb_convert_encoding($string, 'utf-8')
                        )
                    )
            );
    }
}
