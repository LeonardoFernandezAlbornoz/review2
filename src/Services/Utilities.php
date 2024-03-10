<?php
namespace App\Services;

use DateTime;
use DateTimeImmutable;
use Knp\Bundle\TimeBundle\DateTimeFormatter;
use Knp\Bundle\TimeBundle\KnpTimeBundle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class Utilities
{


    function __construct(#[Autowire("%imageFile%")] private string $image, private DateTimeFormatter $dateTimeFormatter)
    {
    }

    function getFile()
    {
        return $this->image;
    }
    function formatDate(DateTimeImmutable $date)
    {

        $actual = new DateTimeImmutable();
        return $this->dateTimeFormatter->formatDiff($actual, $date);
    }
}