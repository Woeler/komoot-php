<?php

declare(strict_types=1);

namespace Woeler\KomootPhp\Enums;

enum TourType: string
{
    case PLANNED = 'tour_planned';
    case COMPLETED = 'tour_recorded';
}
