<?php

declare(strict_types=1);

namespace Woeler\KomootPhp\Enums;

enum PrivacySetting: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case FRIENDS = 'friends';
}
