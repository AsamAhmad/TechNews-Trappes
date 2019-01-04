<?php

namespace App\Service\Twig;


use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    public const NB_SUMMARY_CHAR = 170;

    public function getFilters()
    {
        return [
            new \Twig_Filter('summary', function($text) {

                # Supprimer les balises HTML
                $string = strip_tags($text);

                # Si mon string est supérieur à 170, je continue
                if(strlen($string) > self::NB_SUMMARY_CHAR) {

                    # Je coupe ma chaine à 170
                    $stringCut = substr($string, 0, self::NB_SUMMARY_CHAR);

                    # Je ne doit pas couper un mot en plein milieu...
                    $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...';

                }

                return $string;

            },['is_safe' => ['html']])
        ];
    }

}